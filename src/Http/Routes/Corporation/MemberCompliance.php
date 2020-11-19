<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Corporation\MemberCompliance\MemberComplianceController;

Route::prefix('compliance')
    ->middleware(['permission:view member compliance'])
    ->group(function () {
        Route::get('', [MemberComplianceController::class, 'index'])->name('corporation.member_compliance');

        Route::get('/{corporation_id}/character', [MemberComplianceController::class, 'getCharacterCompliance'])->name('character.compliance');
        Route::get('/{corporation_id}/user', [MemberComplianceController::class, 'getUserCompliance'])->name('user.compliance');
        Route::get('/{corporation_id}/user/missing_characters', [MemberComplianceController::class, 'getMissingCharacters'])->name('missing.characters.compliance');
    });
