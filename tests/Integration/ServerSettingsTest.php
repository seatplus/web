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

use Illuminate\Support\Facades\Event;
use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

it('has users list', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/UserList'));
});

it('has server scopes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('settings.scopes'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

test('one can impersionate', function () {
    $user_two = Event::fakeFor(fn () => User::factory()->create());

    $response = test()->actingAs(test()->test_user)
        ->get(route('impersonate.start', $user_two->id));

    test()->assertAuthenticatedAs($user_two);
});

test('one can stop impersionate', function () {
    $user_two = Event::fakeFor(fn () => User::factory()->create());

    test()->actingAs(test()->test_user)
        ->get(route('impersonate.start', $user_two->id));

    test()->assertAuthenticatedAs($user_two);

    test()->actingAs($user_two)
        ->get(route('impersonate.stop'));

    test()->assertAuthenticatedAs(test()->test_user);
});
