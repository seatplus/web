<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Seatplus\Web\Tests\TestCase;

class SidebarTest extends TestCase
{
    public function setUp(): void
    {

        parent::setUp();

        //Permission::findOrCreate('superuser');
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function user_without_superuser_does_not_see_access_control()
    {

        $this->actingAs($this->test_user);

        $sidebar = (new SidebarEntries)->filter();

        $this->assertFalse(isset($sidebar['Access Control']));
    }

    /** @test */
    public function user_with_superuser_does_see_access_control()
    {

        $this->actingAs($this->test_user);

        $this->test_user->givePermissionTo('superuser');

        $sidebar = (new SidebarEntries)->filter();

        $this->assertTrue(isset($sidebar['Access Control']));
    }

    /** @test */
    public function user_with_view_access_control_does_see_access_control()
    {

        $this->actingAs($this->test_user);

        Permission::create(['name' => 'view access control']);

        $this->test_user->givePermissionTo('view access control');

        $sidebar = (new SidebarEntries)->filter();

        $this->assertTrue(isset($sidebar['Access Control']));
    }

    /** @test */
    public function user_without_view_access_control_does_see_access_control()
    {

        $this->actingAs($this->test_user);

        Permission::create(['name' => 'view access control']);

        //$this->test_user->givePermissionTo('view access control');

        $sidebar = (new SidebarEntries)->filter();

        $this->assertFalse($this->test_user->can('view access control'));
        $this->assertFalse(isset($sidebar['Access Control']));
    }

    /** @test */
    public function user_with_director_role_can_see_membertracking()
    {

        $this->actingAs($this->test_user);

        $character_role = $this->test_character->roles;
        $character_role->roles = ['Director'];
        $character_role->save();

        $this->assertTrue($character_role->hasRole('roles', 'Director'));

        $sidebar = (new SidebarEntries)->filter();

        $this->assertTrue(isset($sidebar['corporation']));
    }

}
