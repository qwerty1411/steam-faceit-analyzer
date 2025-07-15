<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SteamLoginController;
use App\Http\Controllers\FaceitController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [FaceitController::class, 'index'])->name('faceit.index');
Route::get('/api/faceit', [FaceitController::class, 'show']);
Route::get('/api/faceit/search', [FaceitController::class, 'searchPlayers']);
Route::get('/api/matches/{matchId}', [\App\Http\Controllers\FaceitController::class, 'getMatch']);

Route::get('/auth/steam', [SteamLoginController::class, 'redirectToSteam']);
Route::get('/auth/steam/callback', [SteamLoginController::class, 'handleSteamCallback'])->name('steam.callback');

Route::get('/api/user', function () {
    return Auth::user();
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return response()->noContent();
});



//Route::get('/auth/faceit', [LoginController::class, 'redirectToFaceit']);
//Route::get('/auth/faceit/callback', [LoginController::class, 'handleFaceitCallback']);
//Route::get('/auth/faceit/redirect', [LoginController::class, 'redirectToFaceit']);


//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

//Route::get('/dashboard', function () {
//    return Inertia::render('Dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//Route::middleware('auth')->group(function () {
//    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//});


require __DIR__.'/auth.php';
