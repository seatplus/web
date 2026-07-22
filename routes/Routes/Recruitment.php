<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckAuthorization;
use Seatplus\Web\Http\Controllers\Recruitment\ApplicationsController;
use Seatplus\Web\Http\Controllers\Recruitment\ApplyController;
use Seatplus\Web\Http\Controllers\Recruitment\ImpersonateRecruit;
use Seatplus\Web\Http\Controllers\Recruitment\JobPortalController;
use Seatplus\Web\Http\Controllers\Recruitment\ManageRecruitmentController;
use Seatplus\Web\Http\Controllers\Recruitment\PostingController;
use Seatplus\Web\Http\Controllers\Recruitment\ReviewInboxController;
use Seatplus\Web\Http\Controllers\Recruitment\WithdrawController;
use Seatplus\Web\Http\Middleware\CheckAffiliationForApplication;

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
            Route::put('/posting/{corporation_id}', 'save')->name('recruitment.posting.save');
        });
    });

// Applicant apply / withdraw for a single character or the whole account — used by the dashboard +
// onboarding posting cards. Kept on the plural `/applications…` so they never collide with the
// portal's `POST /apply` (recruitment.apply) or `DELETE /application/{id}` (recruitment.withdraw).
Route::controller(ApplicationsController::class)->group(function () {
    Route::post('/applications', 'apply')->name('post.application');
    Route::delete('/applications/character/{character_id}', 'pullCharacterApplication')->name('delete.character.application');
    Route::delete('/applications/user', 'pullUserApplication')->name('delete.user.application');
});

// Reviewer actions on a single application (the review detail + its inline controls). The recruiter
// must be affiliated with the applicant's corporation and hold the review permission.
Route::controller(ApplicationsController::class)->group(function () {
    Route::middleware('permission:can accept or deny applications')
        ->group(function () {
            Route::get('/application/{character_id}/update', 'getBatchUpdate')->name('get.batch_update');
            Route::post('/application/{character_id}/update', 'dispatchBatchUpdate')->name('dispatch.batch_update');
        });

    Route::middleware(CheckAffiliationForApplication::class.':can accept or deny applications')
        ->group(function () {
            Route::get('/application/{application_id}', 'getApplication')->name('get.application');
            Route::post('/application/{application_id}', 'reviewApplication')->name('review.application');
            Route::put('/application/{application_id}', 'addComment')->name('comment.application');
        });
});

Route::middleware(CheckAuthorization::class.':can accept or deny applications')
    ->get('/application/{application_id}/impersonate', ImpersonateRecruit::class)
    ->name('impersonate.recruit');
