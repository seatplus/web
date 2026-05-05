<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies ManageControlGroupMembersController to unauthenticated user', function () {
    test()->get(route('acl.manage', test()->role->id))
        ->assertRedirect();
});

it('denies ManageControlGroupMembersController without permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.manage', test()->role->id))
        ->assertForbidden();
});

it('admin can access ManageControlGroupMembersController', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.manage', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/ManageControlGroup')
            ->has('role')
        );
});

it('renders role with members and affiliations', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    $second_user = User::factory()->create();

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.member.add', [test()->role->id, $second_user->id]))
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->get(route('acl.manage', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/ManageControlGroup')
            ->where('role.title', 'test')
        );
});
