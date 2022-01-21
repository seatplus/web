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
use Seatplus\Web\Http\Middleware\CheckAffiliationForApplication;

Route::prefix('recruitment')
    ->group(function () {
        Route::post('/apply', [ApplicationsController::class, 'apply'])->name('post.application');
        Route::delete('/application/character/{character_id}', [ApplicationsController::class, 'pullCharacterApplication'])->name('delete.character.application');
        Route::delete('/application/user/', [ApplicationsController::class, 'pullUserApplication'])->name('delete.user.application');

        /* Senior HR */
        Route::middleware(['permission:can open or close corporations for recruitment,director'])
            ->controller(EnlistmentsController::class)
            ->group(function () {
                Route::post('/', 'create')->name('create.corporation.recruitment');
                Route::get('/{corporation_id}', 'edit')->name('edit.enlistment');
                Route::delete('/{corporation_id}', 'delete')->name('delete.enlistment');

                Route::post('/watchlist/{corporation_id}', 'updateWatchlist')->name('update.watchlist');
            });

        /* Junior HR */
        Route::middleware('permission:can open or close corporations for recruitment|can accept or deny applications,director')
            ->get('', GetRecruitmentIndexController::class)->name('corporation.recruitment');

        Route::controller(ApplicationsController::class)
            ->group(function () {
                Route::middleware('permission:can accept or deny applications')
                    ->group(function () {
                        Route::get('/applications/{corporation_id}/open', 'getOpenCorporationApplications')->name('open.corporation.applications');
                        Route::get('/applications/{corporation_id}/closed', 'getClosedCorporationApplications')->name('closed.corporation.applications');

                        Route::get('/update/{character_id}', 'getBatchUpdate')->name('get.batch_update');
                        Route::post('/update/{character_id}', 'dispatchBatchUpdate')->name('dispatch.batch_update');
                    });

                Route::middleware(CheckAffiliationForApplication::class . ':can accept or deny applications')
                    ->group(function () {
                        Route::get('/application/{application_id}', [ApplicationsController::class, 'getApplication'])->name('get.application');
                        Route::post('/application/{application_id}', [ApplicationsController::class, 'reviewApplication'])->name('review.application');
                        Route::put('/application/{application_id}', 'addComment')->name('comment.application');
                    });
            });

        Route::middleware(CheckAffiliationForApplication::class . ':can accept or deny applications')
            ->get('/impersonate/{application_id}', ImpersonateRecruit::class)
            ->name('impersonate.recruit');
    });
