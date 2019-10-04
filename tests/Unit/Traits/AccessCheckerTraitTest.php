<?php

namespace Seatplus\Web\Tests\Unit\Traits;

use Seatplus\Web\Models\Permissions\Permission;
use Seatplus\Web\Models\Permissions\Role;
use Seatplus\Web\Tests\TestCase;

class AccessCheckerTraitTest extends TestCase
{
    protected $role;

    protected $permission;

    protected function setUp(): void
    {

        parent::setUp();

        $this->role = Role::create(['name' => 'writer']);
        $this->permission = Permission::create(['name' => 'edit articles']);

        $this->role->givePermissionTo($this->permission);
        $this->test_user->assignRole($this->role);

    }

    /** @test */
    public function haveAccessToCharacterTest()
    {
        $this->role->affiliations()->create([
            'allowed' => collect([
                'character_ids' => [12345]
            ])
        ]);


        $this->assertTrue($this->test_user->hasAccessTo($this->permission->name, 12345));
    }

    /** @test */
    public function haveNoAccessToCharacterTest()
    {
        $this->role->affiliations()->create([
            'allowed' => collect([
                'character_ids' => [12345]
            ])
        ]);


        $this->assertFalse($this->test_user->hasAccessTo($this->permission->name, 54321));
    }


}