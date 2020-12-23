<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Character\ContactsController;

Route::prefix('contacts')
    ->group(function () {
        Route::get('',[ContactsController::class, 'index'])->name('character.contacts');
        Route::get('/{id}', [ContactsController::class, 'detail'])->name('character.contacts.detail');
    });

