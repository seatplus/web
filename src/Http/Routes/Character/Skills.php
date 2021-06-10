<?php


use Illuminate\Support\Facades\Route;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Web\Http\Controllers\Character\SkillsController;

Route::prefix('skills')
    ->group(function () {
        Route::get('', [SkillsController::class, 'index'])->name('character.skills');

        Route::middleware(sprintf('permission:%s', config('eveapi.permissions.' . Skill::class)))
            ->group(function () {
                Route::get('/{character_id}/skills', [SkillsController::class, 'skills'])->name('get.character.skills');
                Route::get('/{character_id}/skillqueue', [SkillsController::class, 'skillQueue'])->name('get.character.skill.queue');
            });
    });