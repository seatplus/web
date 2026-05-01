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

it('denies UpdateManualGroupController to unauthenticated user', function () {
    test()->postJson(route('acl.update.manual', test()->role->id), ['name' => 'renamed'])
        ->assertUnauthorized();
});

it('denies UpdateManualGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), ['name' => 'renamed'])
        ->assertForbidden();
});

it('updates manual role name', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), ['name' => 'new manual name'])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->name)->toBe('new manual name');
});

it('sets manual role type when type was different', function () {
    assignPermissionToTestUser('administrate access control groups');

    // Set to automatic first via HTTP
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertRedirect();

    expect(test()->role->fresh()->type)->toBe(RoleType::AUTOMATIC);

    // Now switch back to manual via HTTP
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), [])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::MANUAL);
});

it('updates manual role affiliations', function () {
    assignPermissionToTestUser('administrate access control groups');

    $corporation = CorporationInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), [
            'affiliated' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->affiliations)->toHaveCount(1)
        ->and(test()->role->affiliations->first()->affiliatable_id)->toBe($corporation->corporation_id);
});

it('clears manual role affiliations via HTTP', function () {
    assignPermissionToTestUser('administrate access control groups');

    $corporation = CorporationInfo::factory()->create();

    // Add an affiliation first
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), [
            'affiliated' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
        ])
        ->assertRedirect();

    expect(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();

    // Clear affiliations by passing empty array
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), ['affiliated' => []])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->affiliations->isEmpty())->toBeTrue();
});
