<?php

use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [\App\Http\Controllers\Public\HomeController::class, 'index'])->name('public.home');
Route::get('/competition/{competition}', [\App\Http\Controllers\Public\CompetitionController::class, 'show'])->name('public.competition.show');
Route::get('/competition/{competition}/calendar', [\App\Http\Controllers\Public\CompetitionController::class, 'calendar'])->name('public.competition.calendar');
Route::get('/team/{team}', [\App\Http\Controllers\Public\TeamController::class, 'show'])->name('public.team.show');

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');