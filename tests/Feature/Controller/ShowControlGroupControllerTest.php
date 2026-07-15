<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
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
            ->has('role.applies_to')
            ->has('role.eligibility')
        );
});

it('round-trips the everything / anyone Doomheim sentinels in the detail resource', function () {
    assignPermissionToTestUser('administrate access control groups');

    // Doomheim (1000001): inverse affiliation = applies to everything; as a criterion = open to all.
    Affiliation::create([
        'role_id' => test()->role->id,
        'affiliatable_id' => 1000001,
        'affiliatable_type' => CorporationInfo::class,
        'type' => 'inverse',
    ]);
    RoleMembership::create([
        'role_id' => test()->role->id,
        'entity_id' => 1000001,
        'entity_type' => CorporationInfo::class,
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.detail', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('role.applies_to.everything', true)
            ->where('role.applies_to.allowed', [])
            ->where('role.applies_to.inverse', [])
            ->where('role.eligibility.anyone', true)
            ->where('role.eligibility.entities', [])
        );
});

it('shows role detail to a plain member (discover flow, not just admins/moderators)', function () {
    RoleMembership::create([
        'role_id' => test()->role->id,
        'entity_type' => User::class,
        'entity_id' => test()->test_user->getKey(),
        'status' => 'active',
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.detail', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleDetail')
            ->where('can_edit', false)
            ->where('role.my_status', 'active')
        );
});

it('shows role detail page to on-request moderator without edit permission', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    test()->actingAs($admin)
        ->postJson(route('acl.update.on-request', test()->role->id), ['name' => test()->role->name])
        ->assertRedirect();

    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->get(route('acl.detail', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleDetail')
            ->where('can_edit', false)
        );
});
