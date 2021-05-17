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
use Seatplus\Web\Http\Controllers\HomeController;
use Seatplus\Web\Http\Controllers\Shared\StopImpersonateController;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;

Route::middleware('web')
    ->group(function () {
        Route::middleware(['auth', CheckRequiredScopes::class])
            ->group(function () {
                Route::get('/home', [HomeController::class, 'home'])->name('home');

                Route::prefix('queue')
                    ->group(function () {
                        include __DIR__ . '/Routes/Queue/Queue.php';
                    });

                Route::prefix('configuration')
                    ->group(function () {
                        include __DIR__ . '/Routes/Configuration/Configuration.php';
                        include __DIR__ . '/Routes/Configuration/UserSettings.php';
                        include __DIR__ . '/Routes/Configuration/ManualLocations.php';
                        Route::middleware(['permission:superuser'])->group(function () {
                            include __DIR__ . '/Routes/Configuration/Schedules.php';
                        });
                    });

                Route::prefix('character')
                    ->group(function () {
                        include __DIR__ . '/Routes/Character/View.php';
                        include __DIR__ . '/Routes/Character/Contact.php';
                        include __DIR__ . '/Routes/Character/Wallet.php';
                        include __DIR__ . '/Routes/Character/Contract.php';
                        include __DIR__ . '/Routes/Character/CorporationHistory.php';
                    });

                Route::prefix('corporation')
                    ->group(function () {
                        include __DIR__ . '/Routes/Corporation/Wallet.php';
                        include __DIR__ . '/Routes/Corporation/MemberTracking.php';
                        include __DIR__ . '/Routes/Corporation/Recruitment.php';
                        include __DIR__ . '/Routes/Corporation/MemberCompliance.php';
                    });

                Route::prefix('acl')
                    ->middleware(['acl-permission:view access control'])
                    ->group(function () {
                        include __DIR__ . '/Routes/AccessControl/View.php';
                    });

                Route::prefix('shared')
                    ->group(function () {
                        include __DIR__ . '/Routes/Shared/Shared.php';
                    });
            });

        Route::middleware('auth')
            ->get('/stop/impersonate', StopImpersonateController::class)
            ->name('impersonate.stop');
    });
