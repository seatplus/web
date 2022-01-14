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
            ->group(function () {
                Route::post('/', [EnlistmentsController::class, 'create'])->name('create.corporation.recruitment');
                Route::get('/{corporation_id}', [EnlistmentsController::class, 'edit'])->name('edit.enlistment');
                Route::delete('/{corporation_id}', [EnlistmentsController::class, 'delete'])->name('delete.enlistment');

                Route::post('/watchlist/{corporation_id}', [EnlistmentsController::class, 'updateWatchlist'])->name('update.watchlist');
            });

        /* Junior HR */
        Route::middleware('permission:can open or close corporations for recruitment|can accept or deny applications,director')
            ->get('', GetRecruitmentIndexController::class)->name('corporation.recruitment');

        Route::middleware('permission:can accept or deny applications')
            ->group(function () {
                Route::get('/applications/{corporation_id}', [ApplicationsController::class, 'getOpenCorporationApplications'])->name('open.corporation.applications');

                Route::get('/update/{character_id}', [ApplicationsController::class, 'getBatchUpdate'])->name('get.batch_update');
                Route::post('/update/{character_id}', [ApplicationsController::class, 'dispatchBatchUpdate'])->name('dispatch.batch_update');
            });

        /* User Applications */
        Route::middleware(CheckAffiliationForApplication::class . ':can accept or deny applications')
            ->group(function () {
                Route::get('/application/{application_id}', [ApplicationsController::class, 'getApplication'])->name('get.application');
                Route::post('/application/{application_id}', [ApplicationsController::class, 'reviewApplication'])->name('review.user.application');

                Route::get('/impersonate/{application_id}', ImpersonateRecruit::class)->name('impersonate.recruit');
            });
    });
