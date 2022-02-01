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

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    //Permission::findOrCreate('superuser');
    test()->test_character->roles()->update(['roles' => ['']]);
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

test('user without superuser does not see access control', function () {
    test()->actingAs(test()->test_user);

    $sidebar = (new SidebarEntries())->filter();

    expect(isset($sidebar['Access Control']))->toBeFalse();
});

test('user with superuser does see access control', function () {
    test()->actingAs(test()->test_user);

    test()->test_user->givePermissionTo('superuser');

    $sidebar = (new SidebarEntries())->filter();

    expect(isset($sidebar['Access Control']))->toBeTrue();
});

test('user with view access control does see access control', function () {
    test()->actingAs(test()->test_user);

    Permission::create(['name' => 'view access control']);

    test()->test_user->givePermissionTo('view access control');

    $sidebar = (new SidebarEntries())->filter();

    expect(isset($sidebar['Access Control']))->toBeTrue();
});

test('user without view access control does see access control', function () {
    test()->actingAs(test()->test_user);

    Permission::create(['name' => 'view access control']);

    //test()->test_user->givePermissionTo('view access control');

    $sidebar = (new SidebarEntries())->filter();

    expect(test()->test_user->can('view access control'))->toBeFalse();
    expect(isset($sidebar['Access Control']))->toBeFalse();
});

test('user with director role can see membertracking', function () {
    test()->actingAs(test()->test_user);

    $character_role = test()->test_character->roles;
    $character_role->roles = ['Director'];
    $character_role->save();

    expect($character_role->hasRole('roles', 'Director'))->toBeTrue();

    $sidebar = (new SidebarEntries())->filter();

    expect(isset($sidebar['corporation']))->toBeTrue();
});

test('user with accountant role can see corporation wallet', function () {
    test()->actingAs(test()->test_user);

    // First check that wallets are not visable
    $sidebar = (new SidebarEntries())->filter();

    test()->assertFalse(in_array('Wallets', data_get($sidebar, 'corporation.entries.*.name', [])));

    // Now give user necessairy role
    \Seatplus\Eveapi\Models\Character\CharacterRole::updateOrCreate([
        'character_id' => test()->test_character->character_id,
    ], [
        'roles' => ['Accountant'],
    ]);

    expect(test()->test_character->refresh()->roles->hasRole('roles', 'Accountant'))->toBeTrue();
    expect(test()->test_character->roles->hasRole('roles', 'Director'))->toBeFalse();

    $sidebar = (new SidebarEntries())->filter();

    test()->assertTrue(in_array('Wallets', data_get($sidebar, 'corporation.entries.*.name')));
});

test('user with director role can see corporation wallet', function () {
    test()->actingAs(test()->test_user);

    // First check that wallets are not visable
    $sidebar = (new SidebarEntries())->filter();

    expect(test()->test_character->refresh()->roles->hasRole('roles', 'Director'))->toBeFalse();

    test()->assertFalse(in_array('Wallets', data_get($sidebar, 'corporation.entries.*.name', [])));

    // Now give user necessairy role
    \Seatplus\Eveapi\Models\Character\CharacterRole::updateOrCreate([
        'character_id' => test()->test_character->character_id,
    ], [
        'roles' => ['Director'],
    ]);

    expect(test()->test_character->refresh()->roles->hasRole('roles', 'Director'))->toBeTrue();

    $sidebar = (new SidebarEntries())->filter();

    test()->assertTrue(in_array('Wallets', data_get($sidebar, 'corporation.entries.*.name')));
});
