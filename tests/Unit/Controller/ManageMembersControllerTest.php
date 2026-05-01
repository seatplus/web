<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    test()->role = Role::findById($role->id);
});

it('denies ManageMembersController to unauthenticated user', function () {
    test()->get(route('manage.acl.members', test()->role->id))
        ->assertRedirect();
});

it('denies ManageMembersController to non-moderator', function () {
    test()->actingAs(test()->test_user)
        ->get(route('manage.acl.members', test()->role->id))
        ->assertForbidden();
});

it('allows a moderator to access ManageMembersController', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->get(route('manage.acl.members', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/ModerateMembers'));
});

it('denies superuser who is not a moderator', function () {
    assignPermissionToTestUser(['superuser']);

    test()->actingAs(test()->test_user)
        ->get(route('manage.acl.members', test()->role->id))
        ->assertForbidden();
});
