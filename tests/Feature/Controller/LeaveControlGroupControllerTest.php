<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    test()->role = Role::find($role->id);

    test()->secondary_user = User::factory()->create();
    test()->secondary_character = test()->secondary_user->characters->first();

    // Refresh to ensure corporation relation is loaded
    test()->test_character = test()->test_character->refresh();
    test()->secondary_character = test()->secondary_character->refresh();
});

/**
 * Helper: add a user as an active on-request role member via the service.
 */
function makeOnRequestMember(Role $role, User $user, int $corporation_id): void
{
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Set up on-request role with criteria
    test()->actingAs($admin)
        ->postJson(route('acl.update.on-request', $role->id), [
            'assigned' => [
                ['entity_id' => $corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    // Apply as user
    test()->actingAs($user)
        ->post(route('acl.apply', $role->id))
        ->assertRedirect();

    // Approve as admin (superuser bypasses canModerate check; also calls handleMembers)
    test()->actingAs($admin)
        ->post(route('acl.approve', [$role->id, $user->id]))
        ->assertRedirect();
}

it('denies LeaveControlGroupController to unauthenticated user', function () {
    test()->delete(route('acl.leave', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();
});

it('returns 403 when trying to leave a manual role', function () {
    $manualRole = Role::create(['name' => 'manual-only']);

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [$manualRole->id, test()->test_user->id]))
        ->assertForbidden();
});

it('user can leave their own on-request role', function () {
    makeOnRequestMember(
        test()->role,
        test()->test_user,
        test()->test_character->corporation->corporation_id
    );

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

it('superuser can kick another user', function () {
    makeOnRequestMember(
        test()->role,
        test()->secondary_user,
        test()->secondary_character->corporation->corporation_id
    );

    expect(test()->secondary_user->fresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['superuser']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->secondary_user->id]))
        ->assertRedirect();

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

it('moderator can kick another user', function () {
    makeOnRequestMember(
        test()->role,
        test()->secondary_user,
        test()->secondary_character->corporation->corporation_id
    );

    expect(test()->secondary_user->fresh()->hasRole(test()->role))->toBeTrue();

    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);
    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->secondary_user->id]))
        ->assertRedirect();

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

it('vanilla user cannot kick another user', function () {
    makeOnRequestMember(
        test()->role,
        test()->secondary_user,
        test()->secondary_character->corporation->corporation_id
    );

    expect(test()->secondary_user->fresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->secondary_user->id]))
        ->assertForbidden();

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeTrue();
});
