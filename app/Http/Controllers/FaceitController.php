<?php

namespace App\Http\Controllers;

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

}
