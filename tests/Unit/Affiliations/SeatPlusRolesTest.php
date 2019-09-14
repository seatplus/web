<?php

namespace Seatplus\Web\Tests\Unit\Affiliations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Seatplus\Web\Models\Permissions\Role;
use Seatplus\Web\Models\User;
use Seatplus\Web\Tests\TestCase;

class SeatPlusRolesTest extends TestCase
{

    /** @test */
    public function userHasNoRolesTest()
    {
        //dd('userHasNoRole',$this->test_user->characters->first()->user);

        $this->assertTrue($this->test_user->roles->isEmpty());

    }

    /** @test */
    public function userHasRoleTest()
    {
        $role = Role::create(['name' => 'derp']);

        $this->test_user->assignRole($role);

        $this->assertTrue($this->test_user->roles->isNotEmpty());
    }

    /** @test */
    public function roleHasNoAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $this->assertNUll($role->affiliations);
    }

    /** @test */
    public function roleHasaAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create();

        $this->assertNotNUll($role->affiliations);
    }

    /** @test */
    public function userIsInAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'allowed' => collect([
                'character_ids' => $this->test_user->id
            ])
        ]);

        $this->assertTrue($role->hasAffiliation($this->test_user));
    }

    /** @test */
    public function characterIsInCharacterAllowedAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'allowed' => collect([
                'character_ids' => [$this->test_user->id, 12345]
            ])
        ]);

        //dd(Arr::flatten($role->affiliations->allowed));

        $this->assertTrue($role->hasAffiliation($this->test_user));
    }

    /** @test */
    public function characterIsInCharacterInversedAffiliationTest()
    {
        //TODO
    }

    /** @test */
    public function characterIsInCharacterForbiddenAffiliationTest()
    {
        //TODO
    }

    // Corporation

    /** @test */
    public function characterIsInCorporationAllowedAffiliationTest()
    {
        //TODO
    }

    /** @test */
    public function characterIsInCorporationInversedAffiliationTest()
    {
        //TODO
    }

    /** @test */
    public function characterIsInCorporationForbiddenAffiliationTest()
    {
        //TODO
    }

    // Alliance
    /** @test */
    public function characterIsInAllianceAllowedAffiliationTest()
    {
        //TODO
    }

    /** @test */
    public function characterIsInAllianceInversedAffiliationTest()
    {
        //TODO
    }

    /** @test */
    public function characterIsInAllianceForbiddenAffiliationTest()
    {
        //TODO
    }


}
