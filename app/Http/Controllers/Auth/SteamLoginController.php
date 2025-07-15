<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SteamLoginController extends Controller
{
    public function redirectToSteam()
    {
        $returnTo = urlencode(route('steam.callback'));
        $params = [
            'openid.ns'         => 'http://specs.openid.net/auth/2.0',
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $returnTo,
            'openid.realm'      => config('app.url'),
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        $query = http_build_query($params);

        return redirect('https://steamcommunity.com/openid/login?' . $query);
    }

    public function handleSteamCallback()
    {
        $params = ['openid.assoc_handle', 'openid.signed', 'openid.sig'];
        foreach ($params as $param) {
            if (!request($param)) {
                return response('Invalid Steam login response.', 400);
            }
        }

        $query = request()->all();
        $query['openid.mode'] = 'check_authentication';

        $response = Http::asForm()->post('https://steamcommunity.com/openid/login', $query);

        if (!str_contains($response->body(), 'is_valid:true')) {
            return response('Steam login validation failed.', 400);
        }

        // Получение SteamID64 из claimed_id
        $claimedId = request('openid.claimed_id');
        preg_match('/https?:\/\/steamcommunity\.com\/openid\/id\/(\d+)/', $claimedId, $matches);

        if (!isset($matches[1])) {
            return response('Cannot extract Steam ID.', 400);
        }

        $steamId = $matches[1];

        // Получение профиля через Steam Web API
        $profile = Http::get('https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v2/', [
            'key'      => env('STEAM_API_KEY'),
            'steamids' => $steamId,
        ])->json()['response']['players'][0] ?? null;

        if (!$profile) {
            return response('Failed to fetch Steam profile.', 500);
        }

        $nickname = $profile['personaname'];
        $avatar   = $profile['avatarfull'];

        // Часы в CS2
        $ownedGames = Http::get('https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/', [
            'key'      => env('STEAM_API_KEY'),
            'steamid'  => $steamId,
            'include_appinfo' => true,
        ])->json();

        $cs2Hours = 0;
        foreach ($ownedGames['response']['games'] ?? [] as $game) {
            if (in_array($game['appid'], [730, 2279720])) { // CS:GO или CS2
                $cs2Hours = round($game['playtime_forever'] / 60, 1);
            }
        }

        // Сохранение пользователя
        $user = User::updateOrCreate(
            ['steam_id' => $steamId],
            [
                'name'          => $nickname,
                'steam_avatar'  => $avatar,
                'hours_in_cs2'  => $cs2Hours,
            ]
        );

        Auth::login($user);

        return redirect('/');
    }
}
