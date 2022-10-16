<?php

use Seatplus\Web\Http\Controllers\Onboarding\OnboardingController;

Route::controller(OnboardingController::class)
    ->group(function () {
        Route::get('/',  'index')->name('onboarding');
        Route::post('/', 'complete')->name('onboarding.complete');
    });