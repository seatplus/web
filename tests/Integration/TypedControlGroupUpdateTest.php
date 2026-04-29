<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Auth\Services\Roles\OnRequestRoleService;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

// ShowControlGroupController

it('denies ShowControlGroupController to unauthenticated user', function () {
    test()->get(route('acl.detail', test()->role->id))
        ->assertRedirect();
});

it('denies ShowControlGroupController without required permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.detail', test()->role->id))
        ->assertForbidden();
});

it('shows role detail page to admin with administrate permission', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->get(route('acl.detail', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleDetail')
            ->where('can_edit', true)
            ->has('role.affiliations')
        );
});

it('shows role detail page to on-request moderator', function () {
    (new OnRequestRoleService(test()->role))->setModerator(test()->test_user);

    test()->actingAs(test()->test_user)
        ->get(route('acl.detail', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleDetail')
            ->where('can_edit', false)
        );
});

// UpdateAutomaticGroupController

it('denies UpdateAutomaticGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [
            'name' => 'renamed',
        ])
        ->assertForbidden();
});

it('updates automatic role type and name', function () {
    assignPermissionToTestUser('administrate access control groups');

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [
            'name' => 'renamed automatic',
        ])
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

// UpdateManualGroupController

it('denies UpdateManualGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), [
            'name' => 'renamed',
        ])
        ->assertForbidden();
});

it('updates manual role name', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.manual', test()->role->id), [
            'name' => 'new manual name',
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->name)->toBe('new manual name');
});

it('sets manual role type when type was different', function () {
    assignPermissionToTestUser('administrate access control groups');

    (new BaseRoleService)->for(test()->role)->automatic()->setRoleType(RoleType::AUTOMATIC);

    expect(test()->role->fresh()->type)->toBe(RoleType::AUTOMATIC);

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

// UpdateOnRequestGroupController

it('denies UpdateOnRequestGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [])
        ->assertForbidden();
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

it('sets on-request role type', function () {
    assignPermissionToTestUser('administrate access control groups');

    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.on-request', test()->role->id), [])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::ON_REQUEST);
});

// UpdateOptInGroupController

it('denies UpdateOptInGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertForbidden();
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
