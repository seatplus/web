<?php

use Seatplus\Web\Http\Controllers\Configuration\UserSettingsController;

Route::get('/settings/user', [UserSettingsController::class, 'index'])->name('user.settings');
Route::post('/settings/user/main_character', [UserSettingsController::class, 'update_main_character'])->name('update.main_character');


