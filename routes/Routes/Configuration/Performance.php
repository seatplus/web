<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckAuthorization;
use Seatplus\Web\Http\Controllers\Configuration\PerformanceController;

Route::middleware([CheckAuthorization::class.':superuser'])
    ->prefix('performance')
    ->controller(PerformanceController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('performance.index');
    });
