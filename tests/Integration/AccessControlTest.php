<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
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

        $response->assertComponent('AccessControl/ControlGroups');
    }

    /** @test */
    public function it_has_edit_control_groups()
    {
        $role = Role::create(['name' => 'test']);

        $this->assignPermissionToTestUser(['view access control', 'create or update or delete access control group']);

        $response = $this->actingAs($this->test_user)
            ->get(route('acl.edit', ['role_id' => $role->id]));


        $response->assertComponent('AccessControl/EditGroup');
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
            ->json('POST', route('acl.create', ['role_id' => 1]), ['name' => 'test']);

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
            ->json('DELETE', route('acl.delete'), [
                'role_id' => $role->id
            ]);

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

        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "allowed" => [
                    [
                        "character_id" => 95725047,
                        "name" => "Herpaderp Aldent"
                    ],
                ],
                "roleName" => $name,
            ]);

        $this->assertDatabaseHas('affiliations',[
            'role_id' => $role->id
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

        $response->assertComponent('AccessControl/ManageControlGroup');
    }

    /** @test */
    public function on_can_sync_control_group_members()
    {
        $role = Role::create(['name' => 'test']);

        //dd($this->test_user->hasRole('test'));

        $this->assertFalse($this->test_user->hasRole('test'));

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('acl.manage.update', ['role_id' => $role->id]), [
                "selectedValues" => [
                    [
                        "id" => $this->test_user->id,
                    ],
                ]
            ]);

        $this->assertTrue($this->test_user->fresh()->hasRole('test'));

        //Remove the member from the access control group again
        $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('acl.manage.update', ['role_id' => $role->id]), [
                ]);

        $this->assertFalse($this->test_user->fresh()->hasRole('test'));

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
