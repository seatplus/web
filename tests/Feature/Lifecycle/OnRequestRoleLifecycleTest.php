<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'on-request role']);
    $this->role = Role::query()->findOrFail($role->id);

    // Dedicated admin so test_user can be the applicant
    $this->admin = User::factory()->create();
    assignPermission($this->admin, ['administrate access control groups']);

    // Ensure corporation relation is available
    $this->test_character = $this->test_character->refresh();
});

it('full lifecycle: configure apply set moderator approve leave', function () {
    // 1. Set on-request type, affiliations and application criteria via HTTP
    $this->actingAs($this->admin)
        ->postJson(route('acl.update.on-request', $this->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
            'assigned' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->fresh()->type)->toBe(RoleType::ON_REQUEST)
        ->and($this->role->fresh()->affiliations->isNotEmpty())->toBeTrue();

    // 2. Applicant applies via HTTP
    assignPermission($this->test_user, ['view access control']);
    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('entity_id', $this->test_user->id)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeTrue();

    // 3. Set moderator via HTTP
    $moderator = User::factory()->create();
    $this->actingAs($this->admin)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('entity_id', $moderator->id)
            ->where('can_moderate', true)
            ->exists()
    )->toBeTrue();

    // 4. Moderator approves applicant via HTTP
    $this->actingAs($moderator)
        ->post(route('acl.approve', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeTrue();

    // 5. Member leaves via HTTP
    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeFalse();
});

it('deny flow: configure apply set moderator deny', function () {
    // Set on-request type, affiliations and criteria via HTTP
    $this->actingAs($this->admin)
        ->postJson(route('acl.update.on-request', $this->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
            'assigned' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    assignPermission($this->test_user, ['view access control']);
    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertRedirect();

    $moderator = User::factory()->create();
    $this->actingAs($this->admin)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    $this->actingAs($moderator)
        ->delete(route('acl.deny', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('entity_id', $this->test_user->id)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeFalse();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeFalse();
});
