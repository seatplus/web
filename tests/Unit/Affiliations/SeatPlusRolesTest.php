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

        $this->assertTrue($role->isAllowed($this->test_user));
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

        $this->assertTrue($role->isAllowed($this->test_user));
    }

    /** @test */
    public function characterIsInCharacterInversedAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'inverse' => collect([
                'character_ids' => [$this->test_user->id, 12345]
            ])
        ]);

        //dd(Arr::flatten($role->affiliations->allowed));

        $this->assertFalse($role->isAllowed($this->test_user));
    }

    /** @test */
    public function characterIsInCharacterForbiddenAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'forbidden' => collect([
                'character_ids' => [$this->test_user->id, 12345]
            ])
        ]);

        $this->assertFalse($role->isAllowed($this->test_user));
    }

    //TODO: Assertion that checks combination of forbidden character and allowed/inverse corporation

    // Corporation

    /** @test */
    public function characterIsInCorporationAllowedAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'allowed' => collect([
                'corporation_ids' => Arr::flatten([$this->test_user->characters->map(function ($char) {
                    return optional($char->character)->corporation_id;
                }), 12345])
            ])
        ]);

        $this->assertTrue($role->isAllowed($this->test_user));
    }

    /** @test */
    public function characterIsInCorporationInversedAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'inverse' => collect([
                'corporation_ids' => Arr::flatten([$this->test_user->characters->map(function ($char) {
                    return optional($char->character)->corporation_id;
                }), 12345])
            ])
        ]);

        $this->assertFalse($role->isAllowed($this->test_user));
    }

    /** @test */
    public function characterIsInCorporationForbiddenAffiliationTest()
    {
        $role = Role::create(['name' => 'derp']);

        $role->affiliations()->create([
            'forbidden' => collect([
                'corporation_ids' => Arr::flatten([$this->test_user->characters->map(function ($char) {
                    return optional($char->character)->corporation_id;
                }), 12345])
            ])
        ]);

        $this->assertFalse($role->isAllowed($this->test_user));
    }

    // Alliance
    /** @test */
    public function characterIsInAllianceAllowedAffiliationTest()
    {
        // TODO Setup Alliance_Info table and set test-character up to have alliance
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
