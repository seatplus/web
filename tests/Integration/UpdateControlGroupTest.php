<?php


namespace Seatplus\Web\Tests\Integration;


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class UpdateControlGroupTest extends TestCase
{

    private Role $role;

    public function setUp(): void
    {

        parent::setUp();

        Queue::fake();

        $role = Role::create(['name' => 'test']);
        $this->role = Role::findById($role->id);
    }

    /** @test */
    public function on_can_update_role_type()
    {

        //dd($this->test_user->hasRole('test'));

        $this->assertFalse($this->test_user->hasRole('test'));

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'automatic',
                    'affiliations' => [],
                    'members' => []
                ]
            ]);

        $this->assertEquals('automatic', $this->role->fresh()->type);
    }

    /** @test */
    public function manual_control_group_adds_member()
    {
        $this->assertFalse($this->test_user->hasRole('test'));

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'manual',
                    'affiliations' => [],
                    'members' => [
                        [
                            'user_id' => $this->test_user->id,
                            'user' => $this->test_user
                        ],
                    ]
                ]
            ]);

        $this->assertTrue($this->test_user->refresh()->hasRole($this->role));
    }

    /** @test */
    public function manual_control_group_removes_member()
    {
        $this->role->activateMember($this->test_user);

        $this->assertTrue($this->test_user->refresh()->hasRole($this->role));

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'manual',
                    'affiliations' => [],
                    'members' => []
                ]
            ]);

        $this->assertFalse($this->test_user->refresh()->hasRole($this->role));
    }

    /** @test */
    public function automatic_control_group_adds_affiliation()
    {

        $this->assertTrue($this->role->acl_affiliations->isEmpty());

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'automatic',
                    'affiliations' => [
                        [
                            'type' => 'corporation',
                            'id' => CorporationInfo::factory()->make()->corporation_id
                        ]
                    ],
                    'members' => []
                ]
            ]);

        $this->assertFalse($this->role->refresh()->acl_affiliations->isEmpty());
    }

    /** @test */
    public function automatic_control_group_removes_affiliation()
    {

        $this->assertTrue($this->role->acl_affiliations->isEmpty());

        $this->role->acl_affiliations()->create([
        'affiliatable_id' => CorporationInfo::factory()->make()->corporation_id,
        'affiliatable_type' => CorporationInfo::class
        ]);

        $this->assertFalse($this->role->refresh() ->acl_affiliations->isEmpty());

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
               "acl" => [
                   "type" => 'automatic',
                   'affiliations' => [],
                   'members' => []
               ]
            ]);

        $this->assertTrue($this->role->refresh()->acl_affiliations->isEmpty());
    }

    /** @test */
    public function on_request_control_group_adds_and_removes_moderators()
    {

        $this->assertTrue($this->role->moderators->isEmpty());

        $this->assignPermissionToTestUser(['view access control', 'manage access control group']);

        $this->assertEquals('manual', $this->role->type);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $this->role->id]), [
                "acl" => [
                    "type" => 'on-request',
                    'moderators' => [
                        [
                            'id' => $this->test_user->id
                        ]
                    ]
                ]
            ]);

        // Test if test user is moderator
        $this->assertTrue($this->role->refresh()->moderators->isNotEmpty());
        $this->assertTrue($this->role->refresh()->isModerator($this->test_user));

        // assert that no affiliations has been created
        $this->assertTrue($this->role->refresh()->acl_affiliations->isEmpty());

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
