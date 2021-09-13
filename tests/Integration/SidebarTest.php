<?php


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class);

beforeEach(function () {
    //Permission::findOrCreate('superuser');
    test()->test_character->roles()->update(['roles' => ['']]);
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
});

test('user without superuser does not see access control', function () {

    test()->actingAs(test()->test_user);

    $sidebar = (new SidebarEntries)->filter();

    test()->assertFalse(isset($sidebar['Access Control']));
});

test('user with superuser does see access control', function () {

    test()->actingAs(test()->test_user);

    test()->test_user->givePermissionTo('superuser');

    $sidebar = (new SidebarEntries)->filter();

    test()->assertTrue(isset($sidebar['Access Control']));
});

test('user with view access control does see access control', function () {

    test()->actingAs(test()->test_user);

    Permission::create(['name' => 'view access control']);

    test()->test_user->givePermissionTo('view access control');

    $sidebar = (new SidebarEntries)->filter();

    test()->assertTrue(isset($sidebar['Access Control']));
});

test('user without view access control does see access control', function () {

    test()->actingAs(test()->test_user);

    Permission::create(['name' => 'view access control']);

    //test()->test_user->givePermissionTo('view access control');

    $sidebar = (new SidebarEntries)->filter();

    test()->assertFalse(test()->test_user->can('view access control'));
    test()->assertFalse(isset($sidebar['Access Control']));
});

test('user with director role can see membertracking', function () {

    test()->actingAs(test()->test_user);

    $character_role = test()->test_character->roles;
    $character_role->roles = ['Director'];
    $character_role->save();

    test()->assertTrue($character_role->hasRole('roles', 'Director'));

    $sidebar = (new SidebarEntries)->filter();

    test()->assertTrue(isset($sidebar['corporation']));
});

test('user with accountant role can see corporation wallet', function () {

    test()->actingAs(test()->test_user);

    // First check that wallets are not visable
    $sidebar = (new SidebarEntries)->filter();

    test()->assertFalse(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name', [])));

    // Now give user necessairy role
    test()->test_character->roles()->update(['roles' => ['Accountant']]);

    test()->assertTrue(test()->test_character->refresh()->roles->hasRole('roles', 'Accountant'));
    test()->assertFalse(test()->test_character->roles->hasRole('roles', 'Director'));

    $sidebar = (new SidebarEntries)->filter();

    test()->assertTrue(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name')));
});

test('user with director role can see corporation wallet', function () {

    test()->actingAs(test()->test_user);

    // First check that wallets are not visable
    $sidebar = (new SidebarEntries)->filter();

    test()->assertFalse(test()->test_character->refresh()->roles->hasRole('roles', 'Director'));

    test()->assertFalse(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name', [])));

    // Now give user necessairy role
    test()->test_character->roles()->update(['roles' => ['Director']]);

    test()->assertTrue(test()->test_character->refresh()->roles->hasRole('roles', 'Director'));

    $sidebar = (new SidebarEntries)->filter();

    test()->assertTrue(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name')));
});
