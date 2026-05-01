<?php

declare(strict_types=1);

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;

it('denies UpdateControlGroupController to unauthenticated user', function () {
    $role = Role::create(['name' => 'test']);

    test()->postJson(route('acl.update', $role->id), ['roleName' => 'new'])
        ->assertUnauthorized();
});

it('denies UpdateControlGroupController without permission', function () {
    $role = Role::create(['name' => 'test']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update', ['role_id' => $role->id]), ['roleName' => 'new name'])
        ->assertForbidden();
});

it('updates role name', function () {
    $role = Role::create(['name' => 'old name']);

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update', ['role_id' => $role->id]), [
            'roleName' => 'new name',
        ]);

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'old name']);
    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'new name']);
});

it('syncs role permissions', function () {
    $role = Role::create(['name' => 'test']);

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
