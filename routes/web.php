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

Route::domain('{team}.app.baseball.test')->middleware(['auth', 'team.access'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Dashboard del equipo: ' . current_team()->name;
    });
});

Route::domain('app.baseball.test')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return 'Dashboard general';
    });
});


require __DIR__ . '/auth.php';
