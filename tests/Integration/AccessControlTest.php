<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Seatplus\Web\Tests\Traits\MockRetrieveEsiDataAction;

uses(MockRetrieveEsiDataAction::class);

it('has control groups', function () {
    assignPermissionToTestUser(['view access control']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.groups'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));
});

it('has list control groups', function () {
    $role = Role::create(['name' => 'test']);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.acl'))
        ->assertOk();
});

it('has edit control groups', function () {
    $role = Role::create(['name' => 'test']);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            'affiliations' => [
                [
                    'category' => 'character',
                    'id' => test()->test_character->character_id,
                    'type' => 'allowed',
                ],
            ],
            'roleName' => $role->name,
        ])
        ->assertRedirect();

    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.edit', ['role_id' => $role->id]))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('AccessControl/EditGroup')
                ->has('affiliations', 1)
        );
});

it('create control groups', function () {
    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    \Pest\Laravel\assertDatabaseMissing('roles', [
        'name' => 'test',
    ]);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('acl.create'), ['name' => 'test']);

    \Pest\Laravel\assertDatabaseHas('roles', [
        'name' => 'test',
    ]);
});

it('deletes control group', function () {
    $role = Role::create(['name' => 'test']);

    \Pest\Laravel\assertDatabaseHas('roles', [
        'name' => 'test',
    ]);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('DELETE', route('acl.delete', ['role_id' => $role->id]));

    \Pest\Laravel\assertDatabaseMissing('roles', [
        'name' => 'test',
    ]);
});

it('updates permissions', function () {
    $name = 'update permissions';
    $role = Role::create(['name' => $name]);

    \Pest\Laravel\assertDatabaseMissing('permissions', [
        'name' => 'character.assets',
    ]);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            'roleName' => $name,
            'permissions' => ['character.assets', 'superuser'],
        ]);

    \Pest\Laravel\assertDatabaseHas('permissions', [
        'name' => 'character.assets',
    ]);

    $permission = Permission::findByName('character.assets');

    test()->actingAs(test()->test_user)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            'roleName' => $name,
            'permissions' => ['superuser'],
        ]);

    \Pest\Laravel\assertDatabaseMissing('role_has_permissions', [
        'permission_id' => $permission->id,
        'role_id' => $role->id,
    ]);
});

it('updates affiliations', function () {
    $name = 'update permissions';
    $role = Role::create(['name' => $name]);

    \Pest\Laravel\assertDatabaseMissing('affiliations', [
        'role_id' => $role->id,
    ]);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    // Adding Affiliation
    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            'affiliations' => [
                [
                    'category' => 'character',
                    'id' => 95_725_047,
                    'type' => 'allowed',
                ],
            ],
            'roleName' => $name,
        ]);

    \Pest\Laravel\assertDatabaseHas('affiliations', [
        'role_id' => $role->id,
        'affiliatable_id' => 95_725_047,
    ]);

    // Delete Affiliation
    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            'allowed' => [],
            'roleName' => $name,
        ]);

    \Pest\Laravel\assertDatabaseMissing('affiliations', [
        'role_id' => $role->id,
        'affiliatable_id' => 95_725_047,
    ]);
});

it('updates name', function () {
    $name = 'update permissions';
    $role = Role::create(['name' => $name]);

    \Pest\Laravel\assertDatabaseHas('roles', [
        'name' => $name,
    ]);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            'allowed' => [
                [
                    'character_id' => 95_725_047,
                    'id' => 95_725_047,
                    'name' => 'Herpaderp Aldent',
                ],
            ],
            'roleName' => 'someOtherName',
        ]);

    \Pest\Laravel\assertDatabaseMissing('roles', [
        'name' => $name,
    ]);
});

test('one can manage control group members', function () {
    $role = Role::create(['name' => 'test']);

    assignPermissionToTestUser(['view access control', 'administrate access control groups']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.manage', ['role_id' => $role->id]));

    $response->assertInertia(fn (Assert $page) => $page->component('AccessControl/ManageControlGroup'));
});

test('moderator can manage applications', function () {
    $role = Role::create(['name' => 'test', 'type' => 'on-request']);

    assignPermissionToTestUser(['view access control']);

    // An admin sets test_user as moderator via HTTP
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [$role->id, test()->test_user->id]))
        ->assertRedirect();

    $response = test()->actingAs(test()->test_user)
        ->get(route('manage.acl.members', ['role_id' => $role->id]));

    $response->assertInertia(fn (Assert $page) => $page->component('AccessControl/ModerateMembers'));

    // List Members
    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.members', ['role_id' => $role->id]))
        ->assertOk();
});

test('setup on request group and save twice', function () {
    // Create Role
    $role = Role::create(['name' => 'test', 'type' => 'on-request']);

    // Assing superuser to test_user
    assignPermissionToTestUser(['superuser']);

    // create second user
    $secondary_user = User::factory()->create();

    // secondary user does not see control group in sidebar
    $sidebar = (new SidebarEntries($secondary_user))->getFilteredEntries();

    expect(data_get($sidebar, 'Access Control.entries.*.route'))->toBeNull();

    // navigate to groups
    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.groups'))
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));

    // open manage control group
    test()->actingAs(test()->test_user)
        ->get(route('acl.manage', $role->id))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('AccessControl/ManageControlGroup')
                ->has(
                    'role',
                    fn (Assert $page) => $page
                        ->where('id', $role->id)
                        ->has(
                            'acl',
                            fn (Assert $page) => $page
                                ->where('moderators', [])
                                ->etc()
                        )
                        ->etc()
                )
        );

    // assign secondary user as moderator via HTTP
    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [$role->id, $secondary_user->id]))
        ->assertRedirect();

    expect($role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();

    // check if secondary user is moderator
    expect((new BaseRoleService)->for($role->refresh())->canModerate($secondary_user))->toBeTrue();

    // reassure moderator does now see control group in sidebar
    $sidebar = (new SidebarEntries($secondary_user))->getFilteredEntries();

    test()->assertNotNull($sidebar->firstWhere('name', 'Access Control'));

    // Try moderating
    $response = test()->actingAs($secondary_user)
        ->get(route('manage.acl.members', ['role_id' => $role->id]))
        ->assertInertia(fn (Assert $page) => $page->component('AccessControl/ModerateMembers'));

    // List Members
    $response = test()->actingAs($secondary_user)
        ->get(route('acl.members', ['role_id' => $role->id]))
        ->assertOk();
});

test('search for character', function () {
    // Assign superuser to test_user
    assignPermissionToTestUser(['superuser']);

    // first get default search results by searching with an empty query string
    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable'))
        ->assertOk();

    $response->assertJsonFragment([
        'id' => test()->test_character->character_id,
        'category' => 'character',
    ]);

    // now search with query-string

    // prepare search

    // create token with search permission
    $token = test()->test_character->refresh_token;
    updateRefreshTokenWithScopes($token, ['esi-search.search_structures.v1']);

    // create esi response mock
    test()->mockRetrieveEsiDataAction([
        'character' => [
            test()->test_character->character_id,
        ],
    ]);

    // Execute Test
    $response = test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable', [
            'query' => test()->test_character->name,
        ]))
        ->assertOk();

    $response->assertJsonFragment([
        'id' => test()->test_character->character_id,
        'category' => 'character',
    ]);
});

// Helpers
