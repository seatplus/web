<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Shared\GetAffiliatedCharactersController;

Route::get('affiliated/characters/{permission}', GetAffiliatedCharactersController::class)->name('get.affiliated.characters');
