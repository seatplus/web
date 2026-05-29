<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();
    assignPermissionToTestUser(['administrate access control groups']);

    $role = Role::create(['name' => 'manual role']);
    test()->role = Role::findById($role->id);
});

it('full lifecycle: create affiliations add member kick member', function () {
    // 1. Role created with MANUAL type by default
    expect(test()->role->type)->toBe(RoleType::MANUAL);

    // 2. Set affiliations via HTTP
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), [
            'affiliated' => [
                [
                    'entity_id' => test()->test_character->character_id,
                    'entity_type' => 'character',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();

    // 3. Add a second user as member via HTTP
    $member = User::factory()->create();

    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeTrue();

    // 4. Set a moderator via HTTP
    $moderator = User::factory()->create();

    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    expect(
        test()->role->roleMemberships()
            ->where('can_moderate', true)
            ->where('entity_id', $moderator->id)
            ->exists()
    )->toBeTrue();

    // 5. Remove moderator via HTTP
    test()->actingAs(test()->test_user)
        ->delete(route('acl.moderator.remove', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    expect(
        test()->role->roleMemberships()
            ->where('can_moderate', true)
            ->where('entity_id', $moderator->id)
            ->exists()
    )->toBeFalse();

    // 6. Remove member via HTTP
    test()->actingAs(test()->test_user)
        ->delete(route('acl.member.remove', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeFalse();
});

it('moderator can add and remove members but cannot manage role settings', function () {
    // Set up a moderator via admin action
    $moderator = User::factory()->create();
    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    $member = User::factory()->create();

    // Moderator CAN add members
    test()->actingAs($moderator)
        ->post(route('acl.member.add', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeTrue();

    // Moderator CAN remove members
    test()->actingAs($moderator)
        ->delete(route('acl.member.remove', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeFalse();

    // But moderator CANNOT change role type/affiliations (admin-only)
    test()->actingAs($moderator)
        ->postJson(route('acl.update.manual', test()->role->id), ['name' => 'renamed'])
        ->assertForbidden();

    // But moderator CAN view the members list
    test()->actingAs($moderator)
        ->get(route('acl.members', test()->role->id))
        ->assertOk();
});
