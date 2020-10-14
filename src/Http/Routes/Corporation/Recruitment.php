<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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

        Route::middleware(CheckUserAffiliationForApplication::class . ':can accept or deny applications')
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
