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
    test()->get(route('acl.create'))->assertRedirect();
});

it('denies the create wizard page without the administrate permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.create'))
        ->assertForbidden();
});

it('renders the create wizard with join methods and available permissions', function () {
    assignPermissionToTestUser('administrate access control groups');
    Permission::findOrCreate('view access control');

    test()->actingAs(test()->test_user)
        ->get(route('acl.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/CreateRole')
            ->has('joinMethods', 4)
            ->has('availablePermissions')
        );
});

it('denies acl.store without the administrate permission', function () {
    test()->actingAs(test()->test_user)
        ->post(route('acl.store'), ['name' => 'test', 'type' => 'manual'])
        ->assertForbidden();

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});

it('creates a fully configured group in one request via acl.store', function () {
    assignPermissionToTestUser('administrate access control groups');
    $corporation = CorporationInfo::factory()->create();
    Permission::findOrCreate('view access control');

    test()->actingAs(test()->test_user)
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

    $role = Role::findByName('Fleet Commanders');

    expect($role->type)->toBe(RoleType::OPT_IN)
        ->and($role->affiliations()->where('type', 'allowed')->exists())->toBeTrue()
        ->and($role->roleMemberships()->where('entity_type', CorporationInfo::class)->exists())->toBeTrue()
        ->and($role->hasPermissionTo('view access control'))->toBeTrue();
});

it('rejects an invalid join method', function () {
    assignPermissionToTestUser('administrate access control groups');

    test()->actingAs(test()->test_user)
        ->post(route('acl.store'), ['name' => 'Bad', 'type' => 'nonsense'])
        ->assertInvalid(['type']);
});
