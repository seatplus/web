<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Web\Http\Controllers\Character\ContractsController;

Route::prefix('contracts')
    ->group(function() {
        Route::get('', [ContractsController::class, 'index'])->name('character.contracts');


        Route::middleware(sprintf('permission:%s', config('eveapi.permissions.' . Contract::class)))
            ->group(function () {
                Route::get('/{character_id}', [ContractsController::class, 'getCharacterContractsDetails'])->name('character.contracts.details');
                Route::get('/{character_id}/contract/{contract_id}', [ContractsController::class, 'getContractDetails'])->name('contract.details');
            });
    });
