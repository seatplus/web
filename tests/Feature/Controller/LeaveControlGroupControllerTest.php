<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Tests\TestCase;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    $this->role = Role::query()->findOrFail($role->id);

    $this->secondary_user = User::factory()->create();
    $this->secondary_character = $this->secondary_user->characters->first();

    // Refresh to ensure corporation relation is loaded
    $this->test_character = $this->test_character->refresh();
    $this->secondary_character = $this->secondary_character->refresh();
});

/**
 * Helper: add a user as an active on-request role member via the service.
 */
function makeOnRequestMember(TestCase $case, Role $role, User $user, int $corporation_id): void
{
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Set up on-request role with criteria
    $case->actingAs($admin)
        ->postJson(route('acl.update.on-request', $role->id), [
            'assigned' => [
                ['entity_id' => $corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    // Apply as user
    $case->actingAs($user)
        ->post(route('acl.apply', $role->id))
        ->assertRedirect();

    // Approve as admin (superuser bypasses canModerate check; also calls handleMembers)
    $case->actingAs($admin)
        ->post(route('acl.approve', [$role->id, $user->id]))
        ->assertRedirect();
}

it('denies LeaveControlGroupController to unauthenticated user', function () {
    $this->delete(route('acl.leave', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();
});

it('returns 403 when trying to leave an automatic role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);
    $this->actingAs($admin)
        ->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});

it('user can leave a manual role they were assigned to', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Convert role to manual and add the user as a member
    $this->actingAs($admin)
        ->postJson(route('acl.update.manual', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($admin)
        ->post(route('acl.member.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeTrue();

    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->refresh()->hasRole($this->role))->toBeFalse();
});

it('user can leave their own on-request role', function () {
    makeOnRequestMember($this,
        $this->role,
        $this->test_user,
        $this->test_character->corporation->corporation_id
    );

    expect($this->test_user->fresh()->hasRole($this->role))->toBeTrue();

    assignPermission($this->test_user, ['view access control']);

    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->refresh()->hasRole($this->role))->toBeFalse();
});

it('superuser can kick another user', function () {
    makeOnRequestMember($this,
        $this->role,
        $this->secondary_user,
        $this->secondary_character->corporation->corporation_id
    );

    expect($this->secondary_user->fresh()->hasRole($this->role))->toBeTrue();

    assignPermission($this->test_user, ['superuser']);

    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->secondary_user->id]))
        ->assertRedirect();

    expect($this->secondary_user->refresh()->hasRole($this->role))->toBeFalse();
});

it('moderator can kick another user', function () {
    makeOnRequestMember($this,
        $this->role,
        $this->secondary_user,
        $this->secondary_character->corporation->corporation_id
    );

    expect($this->secondary_user->fresh()->hasRole($this->role))->toBeTrue();

    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);
    $this->actingAs($admin)
        ->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    assignPermission($this->test_user, ['view access control']);

    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->secondary_user->id]))
        ->assertRedirect();

    expect($this->secondary_user->refresh()->hasRole($this->role))->toBeFalse();
});

it('vanilla user cannot kick another user', function () {
    makeOnRequestMember($this,
        $this->role,
        $this->secondary_user,
        $this->secondary_character->corporation->corporation_id
    );

    expect($this->secondary_user->fresh()->hasRole($this->role))->toBeTrue();

    assignPermission($this->test_user, ['view access control']);

    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->secondary_user->id]))
        ->assertForbidden();

    expect($this->secondary_user->refresh()->hasRole($this->role))->toBeTrue();
});
