<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckAuthorization;
use Seatplus\Web\Http\Controllers\Recruitment\ApplyController;
use Seatplus\Web\Http\Controllers\Recruitment\JobPortalController;
use Seatplus\Web\Http\Controllers\Recruitment\ManageRecruitmentController;
use Seatplus\Web\Http\Controllers\Recruitment\PostingController;
use Seatplus\Web\Http\Controllers\Recruitment\ReviewInboxController;
use Seatplus\Web\Http\Controllers\Recruitment\WithdrawController;

// The central job portal — cross-corporation, applicant-facing. Gated only by the outer auth +
// required-scopes + onboarding middleware, so any compliant user can browse and apply.
Route::get('', JobPortalController::class)->name('recruitment.portal');
Route::post('/apply', ApplyController::class)->name('recruitment.apply');
Route::delete('/application/{application_id}', WithdrawController::class)->name('recruitment.withdraw');

// Reviewer inbox — applications waiting at a stage the signed-in user's control group handles.
Route::middleware(CheckAuthorization::class.':can accept or deny applications,director')
    ->get('/reviews', ReviewInboxController::class)
    ->name('recruitment.reviews');

// HR/recruiter workspace — separate from the applicant portal, gated by the manage permission +
// director role (superuser bypasses).
Route::middleware(CheckAuthorization::class.':can open or close corporations for recruitment,director')
    ->group(function () {
        Route::get('/manage', ManageRecruitmentController::class)->name('recruitment.manage');

        Route::controller(PostingController::class)->group(function () {
            Route::post('/posting', 'open')->name('recruitment.posting.open');
            Route::delete('/posting/{corporation_id}', 'close')->name('recruitment.posting.close');
            Route::put('/posting/{corporation_id}/stages', 'updateStages')->name('recruitment.posting.stages');
        });
    });
