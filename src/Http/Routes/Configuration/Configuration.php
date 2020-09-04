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
use Seatplus\Web\Http\Controllers\Configuration\CommandsController;
use Seatplus\Web\Http\Controllers\Configuration\SeatPlusController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\CreateController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\CreateGlobalSsoScopesController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\DeleteGlobalSsoScopesController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\EditController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\OverviewController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\SsoSettingsController;
use Seatplus\Web\Http\Controllers\Configuration\SsoSettings\ViewGlobalScopesController;

Route::middleware(['permission:superuser'])->group(function () {
    Route::get('/settings', [SeatPlusController::class, 'settings'])->name('server.settings');

    Route::post('/cache/clear', [CommandsController::class, 'clear'])->name('cache.clear');

    Route::get('/start/impersonate/{user_id}', [SeatPlusController::class, 'impersonate'])->name('impersonate.start');

    //TODO: create own controller for server
    Route::get('/settings/navigation', [SeatPlusController::class, 'navigation'])->name('settings.navigation');

    Route::get('/settings/scopes', OverviewController::class)->name('settings.scopes');
    Route::get('/settings/scopes/create', [CreateController::class, 'view'])->name('view.create.scopes');
    Route::post('/settings/scopes/create', [CreateController::class, 'create'])->name('create.scopes');

    Route::post('/settings/scopes/create/global', CreateGlobalSsoScopesController::class)->name('create.global.scopes');
    Route::get('/settings/scopes/global', ViewGlobalScopesController::class)->name('view.global.scopes');
    Route::delete('/settings/scopes/global', DeleteGlobalSsoScopesController::class)->name('delete.global.scopes');

    Route::get('/settings/scopes/{entity_id}/edit', EditController::class)->name('edit.scopes.settings');

    Route::delete('/settings/scopes/{entity_id}', [SsoSettingsController::class, 'deleteSsoScopeSetting'])->name('delete.settings.scopes');

    Route::get('/search/{searchParam}', [SsoSettingsController::class, 'searchAllianceCorporations'])->name('search.alliance.corporation');
});

// Route must not be protected
Route::get('/stop/impersonate', [SeatPlusController::class, 'stopImpersonate'])->name('impersonate.stop');
