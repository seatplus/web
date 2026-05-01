<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Role;

it('denies EditControlGroupController to unauthenticated user', function () {
    $role = Role::create(['name' => 'test']);

    test()->get(route('acl.edit', $role->id))
        ->assertRedirect();
});

it('denies EditControlGroupController without permission', function () {
    $role = Role::create(['name' => 'test']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.edit', $role->id))
        ->assertForbidden();
});

it('renders the edit page for a control group', function () {
    $role = Role::create(['name' => 'test']);

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.edit', $role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/EditGroup'));
});
