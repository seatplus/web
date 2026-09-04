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
    $this->role = Role::query()->findOrFail($role->id);
});

it('denies ManageRoleController to unauthenticated user', function () {
    $this->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertUnauthorized();
});

it('denies ManageRoleController without permission', function () {
    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertForbidden();
});

it('updates an automatic role', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    expect($this->role->type)->toBe(RoleType::MANUAL);

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), ['name' => 'renamed automatic'])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->refresh()->type)->toBe(RoleType::AUTOMATIC);
    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'renamed automatic']);
});

it('updates a manual role', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.manual', $this->role->id), ['name' => 'renamed manual'])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->refresh()->type)->toBe(RoleType::MANUAL);
    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'renamed manual']);
});

it('updates an on-request role', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.on-request', $this->role->id), [])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->refresh()->type)->toBe(RoleType::ON_REQUEST);
});

it('updates an opt-in role', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.opt-in', $this->role->id), [])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->refresh()->type)->toBe(RoleType::OPT_IN);
});

it('updates automatic role with affiliated corporations', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    $corporation = CorporationInfo::factory()->create();

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [
            'affiliated' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
        ])
        ->assertRedirect();

    \Pest\Laravel\assertDatabaseHas('affiliations', [
        'role_id' => $this->role->id,
        'affiliatable_id' => $corporation->corporation_id,
    ]);
});

it('updates on-request role with affiliated alliances', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    $alliance = AllianceInfo::factory()->create();

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.on-request', $this->role->id), [
            'affiliated' => [
                ['entity_id' => $alliance->alliance_id, 'entity_type' => 'alliance', 'affiliation_type' => 'allowed'],
            ],
        ])
        ->assertRedirect();

    \Pest\Laravel\assertDatabaseHas('affiliations', [
        'role_id' => $this->role->id,
        'affiliatable_id' => $alliance->alliance_id,
    ]);
});
