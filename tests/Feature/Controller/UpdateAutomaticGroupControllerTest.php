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

it('denies UpdateAutomaticGroupController to unauthenticated user', function () {
    test()->postJson(route('acl.update.automatic', test()->role->id), ['name' => 'renamed'])
        ->assertUnauthorized();
});

it('denies UpdateAutomaticGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), ['name' => 'renamed'])
        ->assertForbidden();
});

it('updates automatic role type and name', function () {
    assignPermissionToTestUser('administrate access control groups');

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), ['name' => 'renamed automatic'])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::AUTOMATIC)
        ->and(test()->role->fresh()->name)->toBe('renamed automatic');
});

it('updates automatic role affiliations', function () {
    assignPermissionToTestUser('administrate access control groups');

    $corporation = CorporationInfo::factory()->create();

    expect(test()->role->affiliations->isEmpty())->toBeTrue();

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => 'allowed',
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});
