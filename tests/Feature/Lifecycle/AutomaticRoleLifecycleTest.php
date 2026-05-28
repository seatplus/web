<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;

beforeEach(function () {
    Queue::fake();
    assignPermissionToTestUser(['administrate access control groups']);

    $role = Role::create(['name' => 'automatic role']);
    test()->role = Role::findById($role->id);
    test()->test_character = test()->test_character->refresh();
});

it('sets automatic type and affiliations via HTTP', function () {
    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [
            'affiliated' => [
                [
                    'entity_id' => test()->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::AUTOMATIC)
        ->and(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});

it('sets assigned criteria for automatic role via HTTP', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [
            'assigned' => [
                [
                    'entity_id' => test()->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    // Verify a role_membership criterion was created for the corporation
    expect(
        test()->role->fresh()->roleMemberships()
            ->where('entity_id', test()->test_character->corporation->corporation_id)
            ->exists()
    )->toBeTrue();
});

it('automatic role rejects moderator assignment', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});
