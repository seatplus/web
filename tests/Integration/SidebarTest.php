<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class SidebarTest extends TestCase
{
    public function setUp(): void
    {

        parent::setUp();

        //Permission::findOrCreate('superuser');
        $this->test_character->roles()->update(['roles' => ['']]);
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
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

    /** @test */
    public function userWithAccountantRoleCanSeeCorporationWallet()
    {

        $this->actingAs($this->test_user);

        // First check that wallets are not visable
        $sidebar = (new SidebarEntries)->filter();

        $this->assertFalse(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name', [])));

        // Now give user necessairy role
        $this->test_character->roles()->update(['roles' => ['Accountant']]);

        $this->assertTrue($this->test_character->refresh()->roles->hasRole('roles', 'Accountant'));
        $this->assertFalse($this->test_character->roles->hasRole('roles', 'Director'));

        $sidebar = (new SidebarEntries)->filter();

        $this->assertTrue(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name')));
    }

    /** @test */
    public function userWithDirectorRoleCanSeeCorporationWallet()
    {

        $this->actingAs($this->test_user);

        // First check that wallets are not visable
        $sidebar = (new SidebarEntries)->filter();

        $this->assertFalse($this->test_character->refresh()->roles->hasRole('roles', 'Director'));

        $this->assertFalse(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name', [])));

        // Now give user necessairy role
        $this->test_character->roles()->update(['roles' => ['Director']]);

        $this->assertTrue($this->test_character->refresh()->roles->hasRole('roles', 'Director'));

        $sidebar = (new SidebarEntries)->filter();

        $this->assertTrue(in_array('Wallets', data_get($sidebar,'corporation.entries.*.name')));
    }



}
