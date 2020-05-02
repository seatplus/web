<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Configuration\Schedules\ScheduleDetail;
use Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesCreate;
use Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesDelete;
use Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesIndex;
use Seatplus\Web\Http\Controllers\Configuration\Schedules\SchedulesPost;

Route::prefix('schedules')
    ->group(function () {
        Route::get('/', SchedulesIndex::class)->name('schedules.index');
        Route::post('/', SchedulesPost::class)->name('schedules.updateOrCreate');
        Route::get('/create', SchedulesCreate::class)->name('schedules.create');
        Route::get('/{schedule_id}/details', ScheduleDetail::class)->name('schedules.details');
        Route::delete('/{id}/delete', SchedulesDelete::class)->name('schedules.delete');
    });
