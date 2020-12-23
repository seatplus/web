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
use Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController;
use Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCorporationsController;
use Seatplus\Web\Http\Controllers\Shared\GetNamesFromIdsController;
use Seatplus\Web\Http\Controllers\Shared\StopImpersonateController;

Route::get('affiliated/characters/{permission}', GetAffiliatedCharactersController::class)->name('get.affiliated.characters');
Route::get('affiliated/corporations/{permission}', GetAffiliatedCorporationsController::class)->name('get.affiliated.corporations');
Route::post('resolve/ids', [GetNamesFromIdsController::class, 'ids'])->name('resolve.ids');
Route::post('resolve/character_affiliations', [GetNamesFromIdsController::class, 'characterAffiliations'])->name('resolve.character_affiliation');
Route::get('resolve/{corporation_id}/corporation_info', [GetNamesFromIdsController::class, 'getCorporationInfo'])->name('resolve.corporation_info');
Route::get('/stop/impersonate', StopImpersonateController::class)->name('impersonate.stop');
