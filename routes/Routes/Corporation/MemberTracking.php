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
use Seatplus\Auth\Http\Middleware\CheckPermissionOrCorporationRole;
use Seatplus\Web\Http\Controllers\Corporation\MemberTracking\MemberTrackingController;
use Seatplus\Web\Http\Middleware\CheckPermissionAndAffiliation;

Route::prefix('tracking')
    ->group(function () {

        Route::middleware(CheckPermissionOrCorporationRole::class . ':view member tracking,director')
            ->get('', [MemberTrackingController::class, 'index'])
            ->name('corporation.member_tracking');

        Route::middleware(CheckPermissionAndAffiliation::class . ':view member tracking,director')
            ->get('/members/{corporation_id}', [MemberTrackingController::class, 'getMemberTracking'])
            ->name('get.corporation.member_tracking');
    });
