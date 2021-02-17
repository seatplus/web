<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Shared\ManualLocationController;

Route::middleware(['permission:manage manual locations'])
    ->prefix('manual_locations')
    ->group(function () {
        Route::get('', [ManualLocationController::class, 'index'])->name('manage.manual_locations');
        Route::get('suggestions', [ManualLocationController::class, 'getSuggestions'])->name('get.manuel_locations.suggestions');
        Route::post('suggestions', [ManualLocationController::class, 'acceptSuggestion'])->name('accept.manuel_locations');
    });
