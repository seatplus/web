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

use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Schedules;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

it('has scope settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('schedules.index'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));
});

test('one can create a schedule', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('schedules.create'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesCreate'));

    \Pest\Laravel\assertDatabaseMissing('schedules', ['job' => 'test-job']);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->postJson(route('schedules.updateOrCreate'), [
            'job' => 'test-job',
            'expression' => 'test-expression',
        ]);

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));

    \Pest\Laravel\assertDatabaseHas('schedules', ['job' => 'test-job']);
});

test('one can view schedule details', function () {
    $schedule = Schedules::create([
        'job' => 'test-job',
        'expression' => 'test-expression',
    ]);

    \Pest\Laravel\assertDatabaseHas('schedules', ['job' => 'test-job']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('schedules.details', $schedule->id));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesDetails'));
});

test('one can delete schedule', function () {
    $schedule = Schedules::create([
        'job' => 'test-job',
        'expression' => 'test-expression',
    ]);

    \Pest\Laravel\assertDatabaseHas('schedules', ['job' => 'test-job']);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->delete(route('schedules.delete', $schedule->id));

    \Pest\Laravel\assertDatabaseMissing('schedules', ['job' => 'test-job']);

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));
});
