<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();
    assignPermission($this->test_user, ['administrate access control groups']);

    $role = Role::create(['name' => 'manual role']);
    $this->role = Role::query()->findOrFail($role->id);
});

it('full lifecycle: create affiliations add member kick member', function () {
    // 1. Role created with MANUAL type by default
    expect($this->role->type)->toBe(RoleType::MANUAL);

    // 2. Set affiliations via HTTP
    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.manual', $this->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $this->test_character->character_id,
                    'entity_type' => 'character',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->fresh()->affiliations->isNotEmpty())->toBeTrue();

    // 3. Add a second user as member via HTTP
    $member = User::factory()->create();

    $this->actingAs($this->test_user)
        ->post(route('acl.member.add', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeTrue();

    // 4. Set a moderator via HTTP
    $moderator = User::factory()->create();

    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('can_moderate', true)
            ->where('entity_id', $moderator->id)
            ->exists()
    )->toBeTrue();

    // 5. Remove moderator via HTTP
    $this->actingAs($this->test_user)
        ->delete(route('acl.moderator.remove', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('can_moderate', true)
            ->where('entity_id', $moderator->id)
            ->exists()
    )->toBeFalse();

    // 6. Remove member via HTTP
    $this->actingAs($this->test_user)
        ->delete(route('acl.member.remove', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeFalse();
});

it('moderator can add and remove members but cannot manage role settings', function () {
    // Set up a moderator via admin action
    $moderator = User::factory()->create();
    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    $member = User::factory()->create();

    // Moderator CAN add members
    $this->actingAs($moderator)
        ->post(route('acl.member.add', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeTrue();

    // Moderator CAN remove members
    $this->actingAs($moderator)
        ->delete(route('acl.member.remove', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeFalse();

    // But moderator CANNOT change role type/affiliations (admin-only)
    $this->actingAs($moderator)
        ->postJson(route('acl.update.manual', $this->role->id), ['name' => 'renamed'])
        ->assertForbidden();
});
