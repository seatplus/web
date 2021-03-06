<?php


namespace Seatplus\Web\Tests\Integration;


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class JoinControlGroupTest extends TestCase
{

    private Role $role;

    public function setUp(): void
    {

        parent::setUp();

        Queue::fake();

        $role = Role::create(['name' => 'test']);
        $this->role = Role::find($role->id);

        //dd($this->test_character)

        /*CharacterAffiliation::factory()->create([
            'character_id' => $this->test_character->character_id
        ]);*/

        /*CharacterUser::factory()->create([
            'user_id' => $this->test_user->id,
            'character_id' => $this->test_character->character_id
        ]);*/

        $this->test_character = $this->test_character->refresh();
    }


    /** @test */
    public function user_can_join_waitlist()
    {


        $this->assertTrue($this->role->acl_affiliations->isEmpty());

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'on-request',
                    'affiliations' => [
                        [
                            'type' => 'corporation',
                            'id' => $this->test_character->corporation->corporation_id
                        ]
                    ],
                    'members' => []
                ]
            ]);

        $this->assertFalse($this->role->refresh()->acl_affiliations->isEmpty());

        $this->assertFalse($this->test_user->hasRole($this->role));

        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.join'), [
                'user_id' => $this->test_user->id,
                'role_id' => $this->role->id
            ]);


        $this->assertFalse($this->test_user->hasRole($this->role));

        $this->assertEquals($this->test_user->id, $this->role->acl_members()->whereStatus('waitlist')->first()->user_id);
    }

    /** @test */
    public function superuser_can_join_immediately()
    {

        $this->assertTrue($this->role->acl_affiliations->isEmpty());

        $this->assignPermissionToTestUser(['superuser']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'on-request',
                    'affiliations' => [
                        [
                            'type' => 'corporation',
                            'id' => $this->test_character->corporation->corporation_id
                        ]
                    ],
                    'members' => []
                ]
            ]);

        $this->assertFalse($this->role->refresh()->acl_affiliations->isEmpty());

        $this->assertFalse($this->test_user->roles->isNotEmpty());

        $response = $this->actingAs($this->test_user)
            ->json('POST', route('acl.join'), [
                'user_id' => $this->test_user->id,
                'role_id' => $this->role->id
            ]);


        $this->assertTrue($this->test_user->refresh()->hasRole($this->role));

        $this->assertEquals($this->test_user->id, $this->role->members()->first()->user_id);

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
