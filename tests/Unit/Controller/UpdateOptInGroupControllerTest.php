<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies UpdateOptInGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertForbidden();
});

it('sets opt-in role type', function () {
    assignPermissionToTestUser('administrate access control groups');

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::OPT_IN);
});

it('updates opt-in role type and affiliations', function () {
    assignPermissionToTestUser('administrate access control groups');

    $alliance = AllianceInfo::factory()->create();

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $alliance->alliance_id,
                    'entity_type' => 'alliance',
                    'affiliation_type' => 'allowed',
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::OPT_IN)
        ->and(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});
