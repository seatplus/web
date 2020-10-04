<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetApplicationsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit;

Route::prefix('recruitment')
    ->group(function () {
        Route::get('/', GetRecruitmentIndexController::class)->name('corporation.recruitment');
        Route::get('/applications/{corporation_id}', GetApplicationsController::class)->name('corporation.applications');

        Route::middleware('permission:can accept or deny applications')
            ->group(function () {

                Route::get('/impersonate/{user}', ImpersonateRecruit::class)->name('impersonate.recruit');
            });

    });
