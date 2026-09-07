<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    $this->role = Role::query()->findOrFail($role->id);
});

it('denies DenyApplicationController to unauthenticated user', function () {
    $this->delete(route('acl.deny', [$this->role->id, 1]))
        ->assertRedirect();
});

it('non-moderator cannot deny an applicant', function () {
    $other_user = User::factory()->create();

    $this->actingAs($other_user)
        ->delete(route('acl.deny', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});

it('moderator can deny an applicant', function () {
    $setup_admin = User::factory()->create();
    assignPermission($setup_admin, ['superuser']);
    $this->actingAs($setup_admin)
        ->postJson(route('acl.update.on-request', $this->role->id), [
            'affiliated' => [
                ['entity_id' => $this->test_character->corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
            'assigned' => [
                ['entity_id' => $this->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    assignPermission($this->test_user, ['view access control']);
    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeTrue();

    $moderator = User::factory()->create();
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);
    $this->actingAs($admin)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    $this->actingAs($moderator)
        ->delete(route('acl.deny', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeFalse();
});
