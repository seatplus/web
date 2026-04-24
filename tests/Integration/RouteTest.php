<?php

use Seatplus\Auth\Models\Permissions\Permission;

it('protects configurations routes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'))
        ->assertForbidden();
});

it('protects access control routes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertForbidden();
});

it('allows superuser on protected access control routes', function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions

    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertOk();
});
