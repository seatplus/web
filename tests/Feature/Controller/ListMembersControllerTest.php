<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies ListMembersController to unauthenticated user', function () {
    test()->get(route('acl.members', test()->role->id))
        ->assertRedirect();
});

it('denies ListMembersController to non-moderator', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.members', test()->role->id))
        ->assertForbidden();
});

it('allows superuser to list members', function () {
    assignPermissionToTestUser(['superuser']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.members', test()->role->id))
        ->assertOk();
});

it('allows moderator to list members', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->get(route('acl.members', test()->role->id))
        ->assertOk();
});
