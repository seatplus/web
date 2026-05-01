<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies UpdateOnRequestGroupController to unauthenticated user', function () {
    test()->postJson(route('acl.update.on-request', test()->role->id), [])
        ->assertUnauthorized();
});

it('denies UpdateOnRequestGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [])
        ->assertForbidden();
});

it('sets on-request role type', function () {
    assignPermissionToTestUser('administrate access control groups');

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::ON_REQUEST);
});

it('updates on-request role type and affiliations', function () {
    assignPermissionToTestUser('administrate access control groups');

    $corporation = CorporationInfo::factory()->create();

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => 'allowed',
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::ON_REQUEST)
        ->and(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});
