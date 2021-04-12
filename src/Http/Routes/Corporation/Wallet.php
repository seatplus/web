<?php

use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Http\Controllers\Corporation\Wallet\CorporationWalletController;

Route::prefix('wallet')
    ->middleware([
        sprintf('permission:%s,%s',
            config('eveapi.permissions.' . WalletJournal::class),
            'Accountant|Junior_Accountant'
        )
    ])
    ->group(function () {
        Route::get('', [CorporationWalletController::class, 'index'])->name('corporation.wallet');
        Route::get('/{corporation_id}/journal/{division_id}', [CorporationWalletController::class, 'journal'])->name('corporation.wallet_journal.detail');
        Route::get('/{corporation_id}/balance/{division_id}', [CorporationWalletController::class, 'balance'])->name('corporation.balance');
        Route::get('/{corporation_id}/transaction/{division_id}', [CorporationWalletController::class, 'transaction'])->name('corporation.wallet_transaction.detail');
    });
