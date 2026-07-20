<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckAuthorization;
use Seatplus\Web\Http\Controllers\Employment\ObservationController;

// Observation during employment — the evolution of member compliance. Gated by the compliance
// permission + director role (superuser bypasses).
Route::middleware(CheckAuthorization::class.':view member compliance,director')
    ->controller(ObservationController::class)
    ->group(function () {
        Route::get('', 'index')->name('employment.observe');
        Route::get('/{corporation_id}/member/{user}', 'member')->name('employment.observe.member');
    });
