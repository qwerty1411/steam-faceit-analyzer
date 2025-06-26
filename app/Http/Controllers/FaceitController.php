<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\FaceitService;
use Inertia\Inertia;

class FaceitController extends Controller
{
    protected FaceitService $faceitService;

    public function __construct(FaceitService $faceitService)
    {
        $this->faceitService = $faceitService;
    }

    public function index()
    {
        return Inertia::render('FaceitTracker');
    }


    public function show(Request $request)
    {
        $nickname = $request->query('nickname');

        if (!$nickname) {
            return response()->json(['error' => 'Nickname is required.'], 400);
        }

        $profile = $this->faceitService->getPlayer($nickname);

        if (!$profile) {
            return response()->json(['error' => 'Player not found or API error.'], 404);
        }

        return response()->json($profile);
    }


    public function getMatch(string $matchId, FaceitService $faceitService): JsonResponse
    {
        $match = $faceitService->getMatch($matchId);

        return $match
            ? response()->json($match)
            : response()->json(['error' => 'Матч не найден'], 404);
    }

    public function searchPlayers(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|min:2',
            'limit' => 'sometimes|integer|min:1|max:5'
        ]);

        $results = $this->faceitService->searchPlayers(
            $request->input('nickname'),
            $request->input('limit', 5)
        );

        return response()->json($results ? $results['items'] : []);
    }

}
