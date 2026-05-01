<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckAuthorization;
use Seatplus\Web\Http\Controllers\AccessControl\ApplicationController;
use Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\EditControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController;
use Seatplus\Web\Http\Controllers\AccessControl\ListMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\ListUserController;
use Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\ManageManualMemberController;
use Seatplus\Web\Http\Controllers\AccessControl\ManageMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController;
use Seatplus\Web\Http\Controllers\AccessControl\SetModeratorController;
use Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\ShowControlGroupsController;
use Seatplus\Web\Http\Controllers\AccessControl\UpdateAutomaticGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\UpdateControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\UpdateManualGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\UpdateOnRequestGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\UpdateOptInGroupController;

Route::get('/', ShowControlGroupsController::class)->name('acl.groups')
    ->middleware([CheckAuthorization::class.':view access control']);
Route::get('/acl', ListControlGroupsController::class)->name('get.acl');
Route::get('/acl/{role_id}/manage_members', ManageMembersController::class)->name('manage.acl.members');
Route::get('acl/{role_id}/members', ListMembersController::class)->name('acl.members');
Route::post('/acl/{role_id}/apply', [ApplicationController::class, 'apply'])->name('acl.apply');
Route::post('/acl/{role_id}/approve/{user_id}', [ApplicationController::class, 'approve'])->name('acl.approve');
Route::delete('/acl/{role_id}/deny/{user_id}', [ApplicationController::class, 'deny'])->name('acl.deny');
Route::delete('/acl/{role_id}/user/{user_id}', LeaveControlGroupController::class)->name('acl.leave');

Route::middleware([CheckAuthorization::class.':administrate access control groups'])->group(function () {
    Route::post('/create', CreateControlGroupController::class)->name('acl.create');

    Route::get('/acl/{role_id}', EditControlGroupController::class)->name('acl.edit');
    Route::post('/acl/{role_id}', UpdateControlGroupController::class)->name('acl.update');
    Route::delete('/acl/{role_id}', DeleteControlGroupController::class)->name('acl.delete');

    Route::get('/search', SearchAffiliatableController::class)->name('acl.search.affiliatable');
});

Route::middleware([CheckAuthorization::class.':administrate access control groups'])->group(function () {
    Route::get('/manage_control_group/{role_id}', [ManageControlGroupMembersController::class, 'index'])->name('acl.manage');

    Route::get('/user', ListUserController::class)->name('list.users');
});

Route::get('/acl/{role_id}/detail', ShowControlGroupController::class)
    ->name('acl.detail');

Route::middleware([CheckAuthorization::class.':administrate access control groups'])->group(function () {
    Route::post('/acl/{role_id}/automatic', UpdateAutomaticGroupController::class)->name('acl.update.automatic');
    Route::post('/acl/{role_id}/manual', UpdateManualGroupController::class)->name('acl.update.manual');
    Route::post('/acl/{role_id}/on-request', UpdateOnRequestGroupController::class)->name('acl.update.on-request');
    Route::post('/acl/{role_id}/opt-in', UpdateOptInGroupController::class)->name('acl.update.opt-in');

    Route::post('/acl/{role_id}/moderator/{user_id}', [SetModeratorController::class, 'add'])->name('acl.moderator.add');
    Route::delete('/acl/{role_id}/moderator/{user_id}', [SetModeratorController::class, 'remove'])->name('acl.moderator.remove');

    Route::post('/acl/{role_id}/member/{user_id}', [ManageManualMemberController::class, 'add'])->name('acl.member.add');
    Route::delete('/acl/{role_id}/member/{user_id}', [ManageManualMemberController::class, 'remove'])->name('acl.member.remove');
});
