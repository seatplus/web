<?php

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Services\Sidebar\SidebarEntries;

beforeEach(function () {
    test()->test_character->roles()->update(['roles' => ['']]);
});

test('user without superuser does not see access control', function () {
    test()->actingAs(test()->test_user);

    $sidebar = (new SidebarEntries)->getFilteredEntries();

    expect($sidebar->pluck('name')->contains('Access Control'))->toBeFalse();
});

test('user with superuser does see access control', function () {
    test()->actingAs(test()->test_user);

    test()->test_user->givePermissionTo('superuser');

    $sidebar = (new SidebarEntries)->getFilteredEntries();

    expect($sidebar->pluck('name')->contains('Access Control'))->toBeTrue();
});

test('user with view access control does see access control', function () {
    test()->actingAs(test()->test_user);

    Permission::create(['name' => 'view access control']);

    test()->test_user->givePermissionTo('view access control');

    $sidebar = (new SidebarEntries)->getFilteredEntries();

    expect($sidebar->pluck('name')->contains('Access Control'))->toBeTrue();
});

test('user without view access control does see access control', function () {
    test()->actingAs(test()->test_user);

    Permission::create(['name' => 'view access control']);

    $sidebar = (new SidebarEntries)->getFilteredEntries();

    expect(test()->test_user->can('view access control'))->toBeFalse()
        ->and($sidebar->pluck('name')->contains('Access Control'))->toBeFalse();
});

test('user with director role can see membertracking', function () {
    test()->actingAs(test()->test_user);

    $character_role = test()->test_character->roles;
    $character_role->roles = ['Director'];
    $character_role->save();

    expect($character_role->hasRole('roles', 'Director'))->toBeTrue();

    $sidebar = (new SidebarEntries)->getFilteredEntries();

    expect($sidebar->pluck('name')->contains('Corporation'))->toBeTrue();
});

test('user with accountant role can see corporation wallet', function () {
    test()->actingAs(test()->test_user);

    // First check that wallets are not visible
    $sidebar = (new SidebarEntries)->getFilteredEntries();

    $corporation = $sidebar->firstWhere('name', 'Corporation');
    expect($corporation)->toBeNull();

    // Now give user necessary role
    CharacterRole::updateOrCreate([
        'character_id' => test()->test_character->character_id,
    ], [
        'roles' => ['Accountant'],
    ]);

    expect(test()->test_character->refresh()->roles->hasRole('roles', 'Accountant'))->toBeTrue()
        ->and(test()->test_character->roles->hasRole('roles', 'Director'))->toBeFalse();

    cache()->flush();
    $sidebar = (new SidebarEntries)->getFilteredEntries();

    $corporation = $sidebar->firstWhere('name', 'Corporation');
    expect($corporation)->not()->toBeNull();

    $entries = data_get($corporation, 'entries');
    expect(collect($entries)->firstWhere('name', 'Wallets'))->not()->toBeNull();
});

test('user with director role can see corporation wallet', function () {
    test()->actingAs(test()->test_user);

    // First check that wallets are not visible
    $sidebar = (new SidebarEntries)->getFilteredEntries();

    expect(test()->test_character->refresh()->roles->hasRole('roles', 'Director'))->toBeFalse();

    $corporation = $sidebar->firstWhere('name', 'Corporation');
    expect($corporation)->toBeNull();

    // Now give user necessary role
    CharacterRole::updateOrCreate([
        'character_id' => test()->test_character->character_id,
    ], [
        'roles' => ['Director'],
    ]);

    cache()->flush();

    expect(test()->test_character->refresh()->roles->hasRole('roles', 'Director'))->toBeTrue();

    $sidebar = (new SidebarEntries)->getFilteredEntries();

    $corporation = $sidebar->firstWhere('name', 'Corporation');
    expect($corporation)->not()->toBeNull();

    $entries = data_get($corporation, 'entries');
    expect(collect($entries)->firstWhere('name', 'Wallets'))->not()->toBeNull();
});
