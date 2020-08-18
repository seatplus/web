<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Corporation\MemberTracking\IndexMemberTrackingController;

Route::prefix('tracking')
    ->group(function () {
        Route::get('/', IndexMemberTrackingController::class)->name('corporation.member_tracking');
    });
