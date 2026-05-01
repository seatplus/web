<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Tests\Traits\MockRetrieveEsiDataAction;

uses(MockRetrieveEsiDataAction::class);

it('shows ControlGroupsIndex to authenticated user with view permission', function () {
    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));
});

it('creates a control group', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);

    test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->postJson(route('acl.create'), ['name' => 'test']);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'test']);
});

it('denies creating a control group without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.create'), ['name' => 'test'])
        ->assertForbidden();

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});

it('deletes a control group', function () {
    $role = Role::create(['name' => 'test']);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'test']);

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->delete(route('acl.delete', ['role_id' => $role->id]));

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});

it('updates role permissions', function () {
    $role = Role::create(['name' => 'test']);

    \Pest\Laravel\assertDatabaseMissing('permissions', ['name' => 'character.assets']);

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update', ['role_id' => $role->id]), [
            'roleName' => $role->name,
            'permissions' => ['character.assets', 'superuser'],
        ]);

    \Pest\Laravel\assertDatabaseHas('permissions', ['name' => 'character.assets']);

    $permission = Permission::findByName('character.assets');

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update', ['role_id' => $role->id]), [
            'roleName' => $role->name,
            'permissions' => ['superuser'],
        ]);

    \Pest\Laravel\assertDatabaseMissing('role_has_permissions', [
        'permission_id' => $permission->id,
        'role_id' => $role->id,
    ]);
});

it('updates role name via acl.update', function () {
    $role = Role::create(['name' => 'old name']);

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update', ['role_id' => $role->id]), [
            'roleName' => 'new name',
        ]);

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'old name']);
});

it('searches for affiliatable entities', function () {
    assignPermissionToTestUser(['superuser']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable'))
        ->assertOk()
        ->assertJsonFragment([
            'id' => test()->test_character->character_id,
            'category' => 'character',
        ]);
});

it('searches affiliatable with ESI query', function () {
    assignPermissionToTestUser(['superuser']);

    $token = test()->test_character->refresh_token;
    updateRefreshTokenWithScopes($token, ['esi-search.search_structures.v1']);

    test()->mockRetrieveEsiDataAction(['character' => [test()->test_character->character_id]]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable', ['query' => test()->test_character->name]))
        ->assertOk()
        ->assertJsonFragment([
            'id' => test()->test_character->character_id,
            'category' => 'character',
        ]);
});
