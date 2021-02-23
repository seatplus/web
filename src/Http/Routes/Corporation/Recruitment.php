<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
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
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit;
use Seatplus\Web\Http\Middleware\CheckUserAffiliationForApplication;

Route::prefix('recruitment')
    ->group(function () {
        Route::get('/list', [EnlistmentsController::class, 'index'])->name('list.open.enlistments');
        Route::post('/apply', [ApplicationsController::class, 'apply'])->name('post.application');
        Route::delete('/application/character/{character_id}', [ApplicationsController::class, 'pullCharacterApplication'])->name('delete.character.application');
        Route::delete('/application/user/', [ApplicationsController::class, 'pullUserApplication'])->name('delete.user.application');

        /* Senior HR */
        Route::middleware(['permission:can open or close corporations for recruitment'])
            ->group(function () {
                Route::post('/', [EnlistmentsController::class, 'create'])->name('create.corporation.recruitment');
                Route::delete('/{corporation_id}', [EnlistmentsController::class, 'delete'])->name('delete.corporation.recruitment');

                Route::get('/watchlist/{corporation_id}', [EnlistmentsController::class, 'watchlist'])->name('get.watchlist');
                Route::post('/watchlist/{corporation_id}', [EnlistmentsController::class, 'updateWatchlist'])->name('update.watchlist');
            });

        /* Junior HR */
        Route::middleware('permission:can open or close corporations for recruitment|can accept or deny applications')
            ->get('', GetRecruitmentIndexController::class)->name('corporation.recruitment');

        Route::middleware('permission:can accept or deny applications')
            ->group(function () {
                Route::get('/applications/{corporation_id}', [ApplicationsController::class, 'getOpenCorporationApplications'])->name('open.corporation.applications');
                Route::get('/shit_list/{corporation_id}', [ApplicationsController::class, 'getAcceptedCorporationApplications'])->name('corporation.shitlist');

                Route::get('/character_application/{character_id}', [ApplicationsController::class, 'getCharacterApplication'])->name('character.application');
                Route::post('/character_application/{character_id}', [ApplicationsController::class, 'reviewCharacterApplication'])->name('review.character.application');
            });

        /* User Applications */
        Route::middleware(CheckUserAffiliationForApplication::class . ':can accept or deny applications')
            ->group(function () {
                Route::get('/user_application/{recruit}', [ApplicationsController::class, 'getUserApplication'])->name('user.application');
                Route::post('/user_application/{recruit}', [ApplicationsController::class, 'reviewUserApplication'])->name('review.user.application');

                Route::get('/impersonate/{recruit}', ImpersonateRecruit::class)->name('impersonate.recruit');
            });
    });
