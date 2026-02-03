<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::domain('{team}.app.baseball.test')->middleware(['web'])->group(function () {
    Route::get('/', [App\Http\Controllers\PublicTeamController::class, 'index'])->name('teams.home');
    Route::get('/players/{player}', [App\Http\Controllers\PublicTeamController::class, 'showPlayer'])->name('players.show');
});

Route::domain('app.baseball.test')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});


require __DIR__ . '/auth.php';
