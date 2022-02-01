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
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    // this assures that we have a user->characters relationship
    Event::fakeFor(fn () => CharacterUser::factory()->create(['user_id' => test()->test_user->id]));
});

it('accepts legal user id', function () {
    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', test()->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);
});

it('does not accepts illegal user id', function () {
    \Pest\Laravel\assertDatabaseMissing('users', ['id' => test()->test_user->id + 1]);

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', test()->test_user->id + 1)
        ->assertExitCode(0);
});

it('creates role', function () {
    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'Superuser']);

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', test()->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'Superuser']);
});

it('assigns super user to user', function () {
    expect(test()->test_user->can('superuser'))->toBeFalse();

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', test()->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);

    expect(test()->test_user->refresh()->can('superuser'))->toBeTrue();
});

test('a second super user can not be assigned', function () {
    //1. give test user superuser
    $role = Role::findOrCreate('Superuser');
    $permission = Permission::findOrCreate('superuser');
    $role->givePermissionTo($permission);
    test()->test_user->assignRole($role);

    $secondary_user = Event::fakeFor(fn () => User::factory()->create());

    test()->artisan('seatplus:assign:superuser', ['characterName' => $secondary_user->main_character])
        ->expectsOutput('Superuser has already been assigned, ask any of the following users to help you out:')
        ->assertExitCode(0);

    expect(test()->test_user->refresh()->can('superuser'))->toBeTrue();
});
