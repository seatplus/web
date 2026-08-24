<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();
});

it('denies the create wizard page to an unauthenticated user', function () {
    $this->get(route('acl.create'))->assertRedirect();
});

it('denies the create wizard page without the administrate permission', function () {
    $this->actingAs($this->test_user)
        ->get(route('acl.create'))
        ->assertForbidden();
});

it('renders the create wizard with join methods and available permissions', function () {
    assignPermission($this->test_user, 'administrate access control groups');
    Permission::findOrCreate('view access control');

    $this->actingAs($this->test_user)
        ->get(route('acl.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/CreateRole')
            ->has('joinMethods', 4)
            // sourced from config('web.permissions'), not the (possibly empty) DB
            ->where('availablePermissions', fn ($permissions) => $permissions->contains('view access control'))
        );
});

it('denies acl.store without the administrate permission', function () {
    $this->actingAs($this->test_user)
        ->post(route('acl.store'), ['name' => 'test', 'type' => 'manual'])
        ->assertForbidden();

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});

it('creates a fully configured group in one request via acl.store', function () {
    assignPermission($this->test_user, 'administrate access control groups');
    $corporation = CorporationInfo::factory()->create();
    Permission::findOrCreate('view access control');

    $this->actingAs($this->test_user)
        ->post(route('acl.store'), [
            'name' => 'Fleet Commanders',
            'type' => 'opt-in',
            'affiliated' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
            'assigned' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
            'permissions' => ['view access control'],
        ])
        ->assertRedirect();

    $role = Role::query()->where('name', 'Fleet Commanders')->firstOrFail();

    expect($role->type)->toBe(RoleType::OPT_IN)
        ->and($role->affiliations()->where('type', 'allowed')->exists())->toBeTrue()
        ->and($role->roleMemberships()->where('entity_type', CorporationInfo::class)->exists())->toBeTrue()
        ->and($role->hasPermissionTo('view access control'))->toBeTrue();
});

it('creates an open-to-all, applies-to-everything group via acl.store', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    // Doomheim (1000001): inverse affiliation = applies to everything; as a criterion = open to all.
    $this->actingAs($this->test_user)
        ->post(route('acl.store'), [
            'name' => 'Everyone',
            'type' => 'opt-in',
            'affiliated' => [
                ['entity_id' => 1000001, 'entity_type' => 'corporation', 'affiliation_type' => 'inverse'],
            ],
            'assigned' => [
                ['entity_id' => 1000001, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    $role = Role::query()->where('name', 'Everyone')->firstOrFail();

    expect($role->affiliations()->where('type', 'inverse')->where('affiliatable_id', 1000001)->exists())->toBeTrue()
        ->and($role->roleMemberships()->where('entity_type', CorporationInfo::class)->where('entity_id', 1000001)->exists())->toBeTrue();
});

it('rejects an invalid join method', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    $this->actingAs($this->test_user)
        ->post(route('acl.store'), ['name' => 'Bad', 'type' => 'nonsense'])
        ->assertInvalid(['type']);
});
