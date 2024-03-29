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
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Http\Controllers\Character\WalletsController;

Route::prefix('wallets')
    ->controller(WalletsController::class)
    ->group(function () {
        Route::get('', [WalletsController::class, 'index'])->name('character.wallets');

        Route::middleware(sprintf('permission:%s', config('eveapi.permissions.' . WalletJournal::class)))
            ->group(function () {

                Route::get('/{character_id}/journal', [WalletsController::class, 'journal'])->name('character.wallet_journal.detail');
                Route::get('/{character_id}/balance', [WalletsController::class, 'balance'])->name('character.balance');
                Route::get('/{character_id}/transaction', [WalletsController::class, 'transaction'])->name('character.wallet_transaction.detail');
            });

        Route::middleware(sprintf('%s:%s', CheckPermissionOrCorporationRole::class, config('eveapi.permissions.' . WalletJournal::class)))
            ->get('/ref_type', 'journalTypes')->name('wallet.journalTypes');

    });
