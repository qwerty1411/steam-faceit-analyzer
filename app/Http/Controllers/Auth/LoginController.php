<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Redirect user to Faceit OAuth page with PKCE + state
     */
    public function redirectToFaceit(Request $request)
    {
        $state = Str::random(40);
        $verifier = Str::random(64);

        $request->session()->put([
            'faceit.oauth_state' => $state,
            'faceit.pkce_verifier' => $verifier,
        ]);

        $challenge = strtr(
            rtrim(base64_encode(hash('sha256', $verifier, true)), '='),
            '+/',
            '-_'
        );

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.faceit.client_id'),
            'redirect_uri' => config('services.faceit.redirect_uri'),
            'scope' => 'openid email profile',
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect("https://accounts.faceit.com/oauth/authorize?{$query}");
    }

    /**
     * Handle Faceit OAuth callback, save or update user, and log in
     */
    public function handleFaceitCallback(Request $request)
    {
        $code = $request->input('code');
        $state = $request->input('state');

        if (!$code || $state !== $request->session()->pull('faceit.oauth_state')) {
            return response('Invalid state or code', 400);
        }

        $verifier = $request->session()->pull('faceit.pkce_verifier');

        // Правильный Basic Auth (обычный Base64)
        $basic = base64_encode(
            config('services.faceit.client_id') . ':' . config('services.faceit.client_secret')
        );

        // Обмен code на токены
        $tokenResponse = Http::withHeaders([
            'Authorization' => "Basic {$basic}",
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->asForm()
            ->post('https://api.faceit.com/auth/v1/oauth/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('services.faceit.redirect_uri'),
                'code_verifier' => $verifier,
            ]);

        if (!$tokenResponse->successful()) {
            Log::error('Faceit token error', $tokenResponse->json());
            return response()->json($tokenResponse->json(), $tokenResponse->status());
        }

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'];
        $refreshToken = $tokenData['refresh_token'] ?? null;

        // Получаем профиль пользователя
        $userResponse = Http::withToken($accessToken)
            ->get('https://api.faceit.com/auth/v1/resources/userinfo');

        if (!$userResponse->successful()) {
            Log::error('Faceit userinfo error', ['status' => $userResponse->status(), 'body' => $userResponse->body()]);
            return response('Error fetching user info', 500);
        }

        $faceitUser = $userResponse->json();
        Log::debug('FACEIT userinfo:', $faceitUser);

        // Маппинг полей из ответа
        $faceitId = $faceitUser['guid'] ?? null;
        $name = $faceitUser['given_name'] ?? null;
        $email = $faceitUser['email'] ?? null;
        $avatar = $faceitUser['picture'] ?? null;
        $nickname = $faceitUser['nickname']
            ?? $faceitUser['preferred_username']
            ?? $name;

        // Сохраняем или обновляем локального пользователя
        $localUser = User::updateOrCreate(
            ['faceit_id' => $faceitId],
            [
                'name' => $name,
                'email' => $email,
                'faceit_nickname' => $nickname,
                'faceit_avatar' => $avatar,
                'faceit_access_token' => $accessToken,
                'faceit_refresh_token' => $refreshToken,
            ]
        );

        Auth::login($localUser);

        return redirect()->intended('/');
    }
}
