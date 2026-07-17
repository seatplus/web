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
use Seatplus\Web\Http\Controllers\AccessControl\AddManualMemberController;
use Seatplus\Web\Http\Controllers\AccessControl\AddModeratorController;
use Seatplus\Web\Http\Controllers\AccessControl\ApplyToRoleController;
use Seatplus\Web\Http\Controllers\AccessControl\ApproveApplicationController;
use Seatplus\Web\Http\Controllers\AccessControl\CreateControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\DenyApplicationController;
use Seatplus\Web\Http\Controllers\AccessControl\JoinOptInRoleController;
use Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController;
use Seatplus\Web\Http\Controllers\AccessControl\ListMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\ListUserController;
use Seatplus\Web\Http\Controllers\AccessControl\ManageRoleController;
use Seatplus\Web\Http\Controllers\AccessControl\RemoveMemberController;
use Seatplus\Web\Http\Controllers\AccessControl\RemoveModeratorController;
use Seatplus\Web\Http\Controllers\AccessControl\RoleHubController;
use Seatplus\Web\Http\Controllers\AccessControl\RoleHubIndexController;
use Seatplus\Web\Http\Controllers\AccessControl\SearchAffiliatableController;

// The unified role hub is the single ACL surface. `acl.groups` is kept as a redirect so the many
// existing route('acl.groups') references (breadcrumbs, post-delete redirects, sidebar history)
// still resolve to the hub index.
Route::redirect('/', '/acl/hub')->name('acl.groups');

// Hub index + per-role hub. Both gate inside their controllers (superuser / view access control /
// administrate / role moderator) so admins and moderators reach them without the view permission.
Route::get('/hub', RoleHubIndexController::class)->name('acl.hub');
Route::get('/hub/{role_id}', RoleHubController::class)->name('acl.hub.show');
Route::get('/acl', ListControlGroupsController::class)->name('get.acl');
Route::get('acl/{role_id}/members', ListMembersController::class)->name('acl.members');
Route::post('/acl/{role_id}/apply', ApplyToRoleController::class)->name('acl.apply');
Route::post('/acl/{role_id}/join', JoinOptInRoleController::class)->name('acl.join');
Route::post('/acl/{role_id}/approve/{user_id}', ApproveApplicationController::class)->name('acl.approve');
Route::delete('/acl/{role_id}/deny/{user_id}', DenyApplicationController::class)->name('acl.deny');
Route::delete('/acl/{role_id}/user/{user_id}', LeaveControlGroupController::class)->name('acl.leave');

Route::middleware([CheckAuthorization::class.':administrate access control groups'])->group(function () {
    Route::get('/create', [CreateControlGroupController::class, 'create'])->name('acl.create');
    Route::post('/create', [CreateControlGroupController::class, 'store'])->name('acl.store');

    Route::delete('/acl/{role_id}', DeleteControlGroupController::class)->name('acl.delete');

    Route::get('/search', SearchAffiliatableController::class)->name('acl.search.affiliatable');
});

Route::middleware([CheckAuthorization::class.':administrate access control groups'])->group(function () {
    Route::get('/user', ListUserController::class)->name('list.users');
});

Route::middleware([CheckAuthorization::class.':administrate access control groups'])->group(function () {
    Route::post('/acl/{role_id}/automatic', ManageRoleController::class)->name('acl.update.automatic')->defaults('type', 'automatic');
    Route::post('/acl/{role_id}/manual', ManageRoleController::class)->name('acl.update.manual')->defaults('type', 'manual');
    Route::post('/acl/{role_id}/on-request', ManageRoleController::class)->name('acl.update.on-request')->defaults('type', 'on-request');
    Route::post('/acl/{role_id}/opt-in', ManageRoleController::class)->name('acl.update.opt-in')->defaults('type', 'opt-in');

    Route::post('/acl/{role_id}/moderator/{user_id}', AddModeratorController::class)->name('acl.moderator.add');
    Route::delete('/acl/{role_id}/moderator/{user_id}', RemoveModeratorController::class)->name('acl.moderator.remove');
});

Route::post('/acl/{role_id}/member/{user_id}', AddManualMemberController::class)->name('acl.member.add');
Route::delete('/acl/{role_id}/member/{user_id}', RemoveMemberController::class)->name('acl.member.remove');
