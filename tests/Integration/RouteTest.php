<?php

use Seatplus\Auth\Models\Permissions\Permission;

it('protects configurations routes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'))
        ->assertForbidden();
});

it('does not protect access control routes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertOk();
});


