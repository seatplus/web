<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController;

Route::prefix('recruitment')
    ->group(function () {
        Route::get('/', GetRecruitmentIndexController::class)->name('corporation.recruitment');

    });
