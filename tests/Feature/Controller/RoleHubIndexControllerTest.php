<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();
});

it('denies the hub index to an unauthenticated user', function () {
    test()->get(route('acl.hub'))
        ->assertRedirect();
});

it('denies the hub index without the view permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.hub'))
        ->assertForbidden();
});

it('renders the hub index for a user with view access control', function () {
    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.hub'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleHubIndex')
            ->where('canManage', false)
            ->where('canCreate', false)
            ->has('myGroups')
            ->has('availableGroups')
            ->has('allGroups'));
});

it('exposes every group to a manager on the hub index', function () {
    assignPermissionToTestUser(['superuser']);

    $managed = Role::findById(Role::create(['name' => 'managed'])->id);
    $managed->update(['type' => RoleType::MANUAL]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.hub'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleHubIndex')
            ->where('canManage', true)
            ->where('canCreate', true)
            ->has('allGroups', 1)
            ->where('allGroups.0.name', 'managed'));
});

it('lists a group the user belongs to under my groups', function () {
    assignPermissionToTestUser(['view access control']);

    $mine = Role::findById(Role::create(['name' => 'mine'])->id);
    $mine->update(['type' => RoleType::OPT_IN]);
    RoleMembership::create([
        'role_id' => $mine->id,
        'entity_type' => User::class,
        'entity_id' => test()->test_user->getKey(),
        'status' => 'active',
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.hub'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('myGroups', 1)
            ->where('myGroups.0.name', 'mine'));
});
