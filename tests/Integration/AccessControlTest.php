<?php


namespace Seatplus\Web\Tests\Integration;


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class AccessControlTest extends TestCase
{
    /** @test */
    public function it_has_control_groups()
    {

        $this->assignPermissionToTestUser(['view access control']);

        $response = $this->actingAs($this->test_user)
            ->get(route('acl.groups'));

        $response->assertOk();

        $response->assertInertia( fn (Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));
    }

    /** @test */
    public function it_has_list_control_groups()
    {
        $role = Role::create(['name' => 'test']);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $response = $this->actingAs($this->test_user)
            ->get(route('get.acl'))
            ->assertOk();
    }

    /** @test */
    public function it_has_edit_control_groups()
    {
        $role = Role::create(['name' => 'test']);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $response = $this->actingAs($this->test_user)
            ->get(route('acl.edit', ['role_id' => $role->id]));

        $response->assertInertia( fn (Assert $page) => $page->component('AccessControl/EditGroup'));
    }

    /** @test */
    public function it_create_control_groups()
    {

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $this->assertDatabaseMissing('roles',[
            'name' => 'test'
        ]);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('acl.create'), ['name' => 'test']);

        $this->assertDatabaseHas('roles',[
            'name' => 'test'
        ]);
    }

    /** @test */
    public function it_deletes_control_group()
    {

        $role = Role::create(['name' => 'test']);

        $this->assertDatabaseHas('roles',[
            'name' => 'test'
        ]);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('DELETE', route('acl.delete', ['role_id' => $role->id]));

        $this->assertDatabaseMissing('roles',[
            'name' => 'test'
        ]);

    }

    /** @test */
    public function it_updates_permissions()
    {
        $name = 'update permissions';
        $role = Role::create(['name' => $name]);

        $this->assertDatabaseMissing('permissions',[
            'name' => 'character.assets'
        ]);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "roleName" => $name,
                "permissions" => ["character.assets", "superuser"]
            ]);

        $this->assertDatabaseHas('permissions',[
            'name' => 'character.assets'
        ]);

        $permission = Permission::findByName("character.assets");

        $this->actingAs($this->test_user)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "roleName" => $name,
                "permissions" => ["superuser"]
            ]);

        $this->assertDatabaseMissing('role_has_permissions',[
            'permission_id' => $permission->id,
            'role_id' => $role->id
        ]);
    }

    /** @test */
    public function it_updates_affiliations()
    {
        $name = 'update permissions';
        $role = Role::create(['name' => $name]);

        $this->assertDatabaseMissing('affiliations',[
            'role_id' => $role->id
        ]);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        // Adding Affiliation
        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "allowed" => [
                    [
                        "character_id" => 95725047,
                        "id" => 95725047,
                        "name" => "Herpaderp Aldent"
                    ],
                ],
                "roleName" => $name,
            ]);

        $this->assertDatabaseHas('affiliations',[
            'role_id' => $role->id,
            'affiliatable_id' => 95725047
        ]);

        // Delete Affiliation
        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "allowed" => [],
                "roleName" => $name,
            ]);

        $this->assertDatabaseMissing('affiliations',[
            'role_id' => $role->id,
            'affiliatable_id' => 95725047
        ]);
    }

    /** @test */
    public function it_updates_name()
    {
        $name = 'update permissions';
        $role = Role::create(['name' => $name]);

        $this->assertDatabaseHas('roles',[
            'name' => $name
        ]);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "allowed" => [
                    [
                        "character_id" => 95725047,
                        "id" => 95725047,
                        "name" => "Herpaderp Aldent"
                    ],
                ],
                "roleName" => 'someOtherName',
            ]);

        $this->assertDatabaseMissing('roles',[
            'name' => $name
        ]);
    }

    /** @test */
    public function one_can_manage_control_group_members()
    {
        $role = Role::create(['name' => 'test']);

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $response = $this->actingAs($this->test_user)
            ->get(route('acl.manage', ['role_id' => $role->id]));

        $response->assertInertia( fn (Assert $page) => $page->component('AccessControl/ManageControlGroup'));
    }

    /** @test */
    public function moderator_can_manage_applications()
    {
        $role = Role::create(['name' => 'test', 'type' => 'on-request']);

        $this->assignPermissionToTestUser(['view access control']);

        $role->acl_affiliations()->create([
            'affiliatable_id' => $this->test_user->id,
            'affiliatable_type' => User::class,
            'can_moderate' => true
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('manage.acl.members', ['role_id' => $role->id]));

        $response->assertInertia( fn (Assert $page) => $page->component('AccessControl/ModerateMembers'));

        // List Members
        $response = $this->actingAs($this->test_user)
            ->get(route('acl.members', ['role_id' => $role->id]))
            ->assertOk();
    }

    /** @test */
    public function setup_on_request_group_and_save_twice()
    {
        // Create Role
        $role = Role::create(['name' => 'test', 'type' => 'on-request']);

        // Assing superuser to test_user
        $this->assignPermissionToTestUser(['superuser']);

        // create second user
        $secondary_user = User::factory()->create();

        // secondary user does not see control group in sidebar
        $sidebar = (new SidebarEntries($secondary_user))->filter();

        $this->assertNull(data_get($sidebar, 'Access Control.entries.*.route'));

        // navigate to groups
        $response = $this->actingAs($this->test_user)
            ->get(route('acl.groups'))
            ->assertInertia( fn(Assert $page) => $page->component('AccessControl/ControlGroupsIndex'));

        // open manage control group
        $this->actingAs($this->test_user)
            ->get(route('acl.manage', $role->id))
            ->assertInertia( fn(Assert $page) => $page
                ->component('AccessControl/ManageControlGroup')
                ->has('role', fn(Assert $page) => $page
                    ->where('id', $role->id)
                    ->has('acl', fn(Assert $page) => $page
                        ->where('moderators', [])
                        ->etc()
                    )
                    ->etc()
                )
            );

        // assign secondary user as moderator
        $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $role->id]), [
                "acl" => [
                    "type" => 'on-request',
                    'moderators' => [
                        [
                            'id' => $secondary_user->id
                        ]
                    ]
                ]
            ]);

        $this->assertTrue($role->refresh()->moderators->isNotEmpty());

        // check if secondary user is moderator
        $this->assertTrue($role->refresh()->isModerator($secondary_user));

        // reassure moderator does now see control group in sidebar
        $sidebar = (new SidebarEntries($secondary_user))->filter();

        $this->assertNotNull(data_get($sidebar, 'Access Control.entries.*.route'));

        // Try moderating
        $response = $this->actingAs($secondary_user)
            ->get(route('manage.acl.members', ['role_id' => $role->id]))
            ->assertInertia( fn (Assert $page) => $page->component('AccessControl/ModerateMembers'));

        // List Members
        $response = $this->actingAs($secondary_user)
            ->get(route('acl.members', ['role_id' => $role->id]))
            ->assertOk();


    }

    private function assignPermissionToTestUser(array $array)
    {
        foreach ($array as $string) {
            $permission = Permission::findOrCreate($string);

            $this->test_user->givePermissionTo($permission);
        }

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

}
