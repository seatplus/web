<?php


use Seatplus\Auth\Models\Permissions\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

test('one can corporation history endpoint', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.history', test()->test_character->character_id))
        ->assertOk();
});
