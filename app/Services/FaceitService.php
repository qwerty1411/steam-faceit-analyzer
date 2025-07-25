<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FaceitService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.faceit.key');
    }

    public function getPlayer(string $nickname): ?array
    {
        $player = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get("https://open.faceit.com/data/v4/players", ['nickname' => $nickname])
            ->json();

        if (!isset($player['player_id'])) {
            return null;
        }

        $stats = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get("https://open.faceit.com/data/v4/players/{$player['player_id']}/stats/cs2")
            ->json();

        $matches = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get("https://open.faceit.com/data/v4/players/{$player['player_id']}/history", [
            'game' => 'cs2',
        ])->json();

        return [
            'profile' => $player,
            'stats' => $stats,
            'matches' => $matches['items'] ?? [],
        ];
    }

    public function getMatch(string $matchId): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get("https://open.faceit.com/data/v4/matches/{$matchId}");

        return $response->successful() ? $response->json() : null;
    }


    public function searchPlayers(string $nickname, int $limit = 5): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get("https://open.faceit.com/data/v4/search/players", [
            'nickname' => $nickname,
            'game' => 'cs2',
            'limit' => $limit
        ]);

        return $response->successful() ? $response->json() : null;
    }

}
