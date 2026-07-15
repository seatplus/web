<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();
});

it('denies ShowControlGroupsController to unauthenticated user', function () {
    test()->get(route('acl.groups'))
        ->assertRedirect();
});

it('denies ShowControlGroupsController without permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertForbidden();
});

it('shows ControlGroupsIndex to user with view access control permission', function () {
    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));
});

it('segments roles into my groups and available-to-join', function () {
    assignPermissionToTestUser(['view access control']);
    $corporationId = test()->test_character->corporation->corporation_id;

    // A self-service group the user is already a member of → "my groups".
    $mine = Role::findById(Role::create(['name' => 'mine'])->id);
    $mine->update(['type' => RoleType::OPT_IN]);
    RoleMembership::create([
        'role_id' => $mine->id,
        'entity_type' => User::class,
        'entity_id' => test()->test_user->getKey(),
        'status' => 'active',
    ]);

    // A self-service group the user is ELIGIBLE for (criteria = their corp) but not in → "available".
    $available = Role::findById(Role::create(['name' => 'available'])->id);
    $available->update(['type' => RoleType::OPT_IN]);
    RoleMembership::create([
        'role_id' => $available->id,
        'entity_type' => CorporationInfo::class,
        'entity_id' => $corporationId,
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('myGroups', 1)
            ->has('availableGroups', 1)
            ->where('myGroups.0.name', 'mine')
            ->where('availableGroups.0.name', 'available')
            ->where('availableGroups.0.can_join', true));
});
