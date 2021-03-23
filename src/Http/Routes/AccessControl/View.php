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
use Seatplus\Web\Http\Controllers\AccessControl\ControlGroupsController;
use Seatplus\Web\Http\Controllers\AccessControl\DeleteControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\JoinControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\LeaveControlGroupController;
use Seatplus\Web\Http\Controllers\AccessControl\ListControlGroupsController;
use Seatplus\Web\Http\Controllers\AccessControl\ListMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\ListUserController;
use Seatplus\Web\Http\Controllers\AccessControl\ManageControlGroupMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\ManageMembersController;
use Seatplus\Web\Http\Controllers\AccessControl\UpdateControlGroupController;

Route::get('/', [ControlGroupsController::class, 'index'])->name('acl.groups');
Route::get('/acl', ListControlGroupsController::class)->name('get.acl');
Route::get('/acl/{role_id}/manage_members', ManageMembersController::class)->name('manage.acl.members');
Route::get('acl/{role_id}/members', ListMembersController::class)->name('acl.members');
Route::post('/', JoinControlGroupController::class)->name('acl.join');
Route::delete('/acl/{role_id}/user/{user_id}', LeaveControlGroupController::class)->name('acl.leave');

Route::middleware(['permission:create or update or delete access control group'])->group(function () {
    Route::post('/create', [ControlGroupsController::class, 'create'])->name('acl.create');

    Route::get('/acl/{role_id}', [ControlGroupsController::class, 'edit'])->name('acl.edit');
    Route::post('/acl/{role_id}', [ControlGroupsController::class, 'update'])->name('acl.update');
    Route::delete('/acl/{role_id}', DeleteControlGroupController::class)->name('acl.delete');
});

Route::middleware(['permission:manage access control group|create or update or delete access control group'])->group(function () {
    Route::get('/manage_control_group/{role_id}', [ManageControlGroupMembersController::class, 'index'])->name('acl.manage');
    Route::post('/manage_control_group/{role_id}', UpdateControlGroupController::class)->name('update.acl.affiliations');

    Route::get('/user', ListUserController::class)->name('list.users');
});
