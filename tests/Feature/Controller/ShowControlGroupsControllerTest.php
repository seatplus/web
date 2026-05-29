<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;

it('denies ShowControlGroupsController to unauthenticated user', function () {
    test()->get(route('acl.groups'))
        ->assertRedirect();
});

it('denies ShowControlGroupsController without permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertForbidden();
});

it('shows ControlGroupsIndex to user with view access control permission', function () {
    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));
});
