<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies ApplyToRoleController to unauthenticated user', function () {
    test()->post(route('acl.apply', test()->role->id))
        ->assertRedirect();
});

it('user can apply to on-request role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);
    test()->actingAs($admin)
        ->postJson(route('acl.update.on-request', test()->role->id), [
            'affiliated' => [
                ['entity_id' => test()->test_character->corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
            'assigned' => [
                ['entity_id' => test()->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->first()
            ?->entity_id
    )->toEqual(test()->test_user->id);
});

it('returns 403 when applying to a manual role', function () {
    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertForbidden();
});

it('returns 403 when applying to an automatic role', function () {
    assignPermissionToTestUser(['administrate access control groups', 'view access control']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertForbidden();
});

it('returns 403 when applying to an opt-in role', function () {
    assignPermissionToTestUser(['administrate access control groups', 'view access control']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertForbidden();
});
