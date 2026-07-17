<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
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
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/ModerateMembers')
            // a plain moderator manages members/applications but not moderators
            ->where('canManageModerators', false)
            ->has('members')
            ->has('applicants')
            ->has('moderators', 1));
});

it('allows a global ACL admin (not a moderator) and enables moderator management', function () {
    // Administrate permission only — not superuser, not a per-role moderator.
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->get(route('manage.acl.members', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/ModerateMembers')
            ->where('canManageModerators', true));
});

it('splits user memberships into members, applicants, and moderators', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    $member = User::factory()->create();
    $applicant = User::factory()->create();

    RoleMembership::create(['role_id' => test()->role->id, 'entity_type' => User::class, 'entity_id' => $member->id, 'status' => 'active']);
    RoleMembership::create(['role_id' => test()->role->id, 'entity_type' => User::class, 'entity_id' => $applicant->id, 'status' => 'pending']);
    RoleMembership::create(['role_id' => test()->role->id, 'entity_type' => User::class, 'entity_id' => $admin->id, 'can_moderate' => true, 'status' => 'active']);

    test()->actingAs($admin)
        ->get(route('manage.acl.members', test()->role->id))
        ->assertInertia(fn (Assert $page) => $page
            ->has('members', 2)      // member + admin (both active)
            ->has('applicants', 1)
            ->has('moderators', 1)); // admin (can_moderate)
});
