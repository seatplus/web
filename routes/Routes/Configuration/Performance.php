<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckPermissionOrCorporationRole;
use Seatplus\Web\Http\Controllers\Configuration\PerformanceController;

Route::middleware([CheckPermissionOrCorporationRole::class . ':superuser'])
    ->prefix('performance')
    ->controller(PerformanceController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('performance.index');
    });
