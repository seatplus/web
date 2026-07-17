<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies ManageRoleController to unauthenticated user', function () {
    test()->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertUnauthorized();
});

it('denies ManageRoleController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertForbidden();
});

it('updates an automatic role', function () {
    assignPermissionToTestUser('administrate access control groups');

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), ['name' => 'renamed automatic'])
        ->assertRedirect(route('acl.hub.show', test()->role->id));

    expect(test()->role->refresh()->type)->toBe(RoleType::AUTOMATIC);
    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'renamed automatic']);
});

it('updates a manual role', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), ['name' => 'renamed manual'])
        ->assertRedirect(route('acl.hub.show', test()->role->id));

    expect(test()->role->refresh()->type)->toBe(RoleType::MANUAL);
    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'renamed manual']);
});

it('updates an on-request role', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [])
        ->assertRedirect(route('acl.hub.show', test()->role->id));

    expect(test()->role->refresh()->type)->toBe(RoleType::ON_REQUEST);
});

it('updates an opt-in role', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertRedirect(route('acl.hub.show', test()->role->id));

    expect(test()->role->refresh()->type)->toBe(RoleType::OPT_IN);
});

it('updates automatic role with affiliated corporations', function () {
    assignPermissionToTestUser('administrate access control groups');

    $corporation = CorporationInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [
            'affiliated' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
        ])
        ->assertRedirect();

    \Pest\Laravel\assertDatabaseHas('affiliations', [
        'role_id' => test()->role->id,
        'affiliatable_id' => $corporation->corporation_id,
    ]);
});

it('updates on-request role with affiliated alliances', function () {
    assignPermissionToTestUser('administrate access control groups');

    $alliance = AllianceInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [
            'affiliated' => [
                ['entity_id' => $alliance->alliance_id, 'entity_type' => 'alliance', 'affiliation_type' => 'allowed'],
            ],
        ])
        ->assertRedirect();

    \Pest\Laravel\assertDatabaseHas('affiliations', [
        'role_id' => test()->role->id,
        'affiliatable_id' => $alliance->alliance_id,
    ]);
});
