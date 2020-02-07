<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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
use Seatplus\Web\Http\Controllers\AccessControl\ManageControllGroupMembersController;

Route::get('/', 'ControlGroupsController@index')->name('acl.groups');
Route::post('/', [ControlGroupsController::class, 'join'])->name('acl.join');

// TODO: get this routes protected
Route::post('/create', 'ControlGroupsController@create')->name('acl.create');
Route::delete('/delete', [ControlGroupsController::class, 'delete'])->name('acl.delete');

Route::get('/edit/{role_id}', 'ControlGroupsController@edit')->name('acl.edit');
Route::post('/edit/{role_id}', [ControlGroupsController::class, 'update'])->name('acl.update');

Route::get('/manage_members/{role_id}', [ManageControllGroupMembersController::class, 'index'])->name('acl.manage');
Route::post('/manage_members/{role_id}', [ManageControllGroupMembersController::class, 'update'])->name('acl.manage.update');
