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

use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController;

Route::prefix('wallet')
    ->middleware([
        sprintf('permission:%s,%s',
            config('eveapi.permissions.' . WalletJournal::class),
            'Accountant|Junior_Accountant'
        ),
    ])
    ->group(function () {
        Route::get('', [CorporationWalletController::class, 'index'])->name('corporation.wallet');
        Route::get('/{corporation_id}/journal/{division_id}', [CorporationWalletController::class, 'journal'])->name('corporation.wallet_journal.detail');
        Route::get('/{corporation_id}/balance/{division_id}', [CorporationWalletController::class, 'balance'])->name('corporation.balance');
        Route::get('/{corporation_id}/transaction/{division_id}', [CorporationWalletController::class, 'transaction'])->name('corporation.wallet_transaction.detail');
    });
