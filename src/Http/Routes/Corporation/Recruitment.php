<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\ApplicationsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\DeleteCharacterApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\DeleteRecruitmentController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\DeleteUserApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\EnlistmentsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetCharacterApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetEnlistmentsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetOpenApplicationsController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetRecruitmentIndexController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\GetUserApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\ImpersonateRecruit;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\PostApplicationController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\PostCharacterReviewController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\PostCreateOpenRecruitmentController;
use Seatplus\Web\Http\Controllers\Corporation\Recruitment\PostUserReviewController;
use Seatplus\Web\Http\Middleware\CheckUserAffiliationForApplication;

Route::prefix('recruitment')
    ->group(function () {



        Route::get('/list', [EnlistmentsController::class, 'index'])->name('list.open.enlistments');
        Route::post('/apply', [ApplicationsController::class, 'apply'])->name('post.application');
        Route::delete('/application/character/{character_id}', [ApplicationsController::class, 'pullCharacterApplication'])->name('delete.character.application');
        Route::delete('/application/user/', [ApplicationsController::class, 'pullUserApplication'])->name('delete.user.application');


        /* Senior HR */
        Route::middleware(['permission:can open or close corporations for recruitment'])
            ->group(function() {

                Route::post('/', [EnlistmentsController::class, 'create'])->name('create.corporation.recruitment');
                Route::delete('/{corporation_id}', [EnlistmentsController::class, 'delete'])->name('delete.corporation.recruitment');
            });


        /* Junior HR */
        Route::middleware('permission:can open or close corporations for recruitment|can accept or deny applications')
            ->get('', GetRecruitmentIndexController::class)->name('corporation.recruitment');

        Route::middleware('permission:can accept or deny applications')
            ->group(function () {

                Route::get('/applications/{corporation_id}', [ApplicationsController::class, 'getOpenCorporationApplications'])->name('open.corporation.applications');

                Route::get('/character_application/{character_id}', [ApplicationsController::class, 'getCharacterApplication'])->name('character.application');
                Route::post('/character_application/{character_id}', [ApplicationsController::class, 'reviewCharacterApplication'])->name('review.character.application');
            });

        /* User Applications */
        Route::middleware(CheckUserAffiliationForApplication::class .':can accept or deny applications')
            ->group(function () {

                Route::get('/user_application/{recruit}', [ApplicationsController::class, 'getUserApplication'])->name('user.application');
                Route::post('/user_application/{recruit}', [ApplicationsController::class, 'reviewUserApplication'])->name('review.user.application');

                Route::get('/impersonate/{recruit}', ImpersonateRecruit::class)->name('impersonate.recruit');
            });

    });
