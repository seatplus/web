<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetCharacterApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetOpenApplicationsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetUserApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\PostCharacterReviewController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\PostUserReviewController;
use Seatplus\Web\Http\Middleware\CheckUserAffiliationForApplication;

Route::prefix('recruitment')
    ->group(function () {
        Route::get('/', GetRecruitmentIndexController::class)->name('corporation.recruitment');
        Route::get('/applications/{corporation_id}', GetOpenApplicationsController::class)->name('open.corporation.applications');

        Route::middleware(CheckUserAffiliationForApplication::class .':can accept or deny applications')
            ->group(function () {

                Route::get('/user_application/{recruit}', GetUserApplicationController::class)->name('user.application');
                Route::post('/user_application/{recruit}', PostUserReviewController::class)->name('review.user.application');
                Route::get('/impersonate/{recruit}', ImpersonateRecruit::class)->name('impersonate.recruit');
            });

        Route::middleware('permission:can accept or deny applications')
            ->group(function () {

                Route::get('/character_application/{character_id}', GetCharacterApplicationController::class)->name('character.application');
                Route::post('/character_application/{character_id}', PostCharacterReviewController::class)->name('review.character.application');
            });

    });
