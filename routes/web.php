<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\PublicLeagueController::class, 'index'])->name('league.home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Obtenemos el dominio desde la configuración
$appDomain = parse_url(config('app.url'), PHP_URL_HOST);

// Rutas de equipos (producción con subdominios)
Route::domain('{team}.' . $appDomain)->middleware(['web', 'tenant'])->group(function () {
    Route::get('/', [App\Http\Controllers\PublicTeamController::class, 'index'])->name('teams.home');
    Route::get('/players/{player}', [App\Http\Controllers\PublicTeamController::class, 'showPlayer'])->name('players.show');
});

// Rutas alternativas para desarrollo local
// Acceso: http://localhost:8000/equipo/{team}/jugador/{id}
Route::middleware(['web', 'tenant'])->prefix('equipo/{team}')->group(function () {
    Route::get('/jugador/{id}', [App\Http\Controllers\PublicTeamController::class, 'showPlayer'])->name('players.show');
    Route::get('/', [App\Http\Controllers\PublicTeamController::class, 'index'])->name('teams.home');
});

Route::domain($appDomain)->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});



require __DIR__ . '/auth.php';
