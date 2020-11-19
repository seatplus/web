<?php


namespace Seatplus\Web\Tests\Integration;


use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Applications;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;


class ComplianceLifeCycleTest extends TestCase
{
    private User $secondary_user;
    private CharacterInfo $secondary_character;
    private User $superuser;

    public function setUp(): void
    {
        parent::setUp();

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->secondary_user = Event::fakeFor(function () {
            return factory(User::class)->create();
        });

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->superuser = Event::fakeFor(function () {
            $user = factory(User::class)->create();

            $permission = Permission::findOrCreate('superuser');

            $user->givePermissionTo($permission);

            // now re-register all the roles and permissions
            $this->app->make(PermissionRegistrar::class)->registerPermissions();

            return $user;
        });

        $this->secondary_character = $this->secondary_user->characters->first();
    }

    /** @test */
    public function user_without_permission_fails_to_see_compliance()
    {
        $response = $this->actingAs($this->secondary_user)
            ->get(route('corporation.member_compliance'))
            ->assertForbidden();

    }

    /** @test */
    public function user_with_permission_sees_component()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.member_compliance'))
            ->assertForbidden();

        $this->givePermissionsToTestUser(['view member compliance']);

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.member_compliance'))
            ->assertOk();

        $response->assertInertia('Corporation/MemberCompliance/MemberCompliance');
    }

    /** @test */
    public function user_with_permission_sees_character_compliance()
    {
        $this->createScopeSetting();

        $this->withoutMiddleware();

        $response = $this->actingAs($this->test_user)
            ->getJson(route('character.compliance', $this->secondary_character->corporation->corporation_id))
            ->assertOk();

        $response->assertJsonCount(1, 'data');

        $response = $this->actingAs($this->test_user)
            ->getJson(route('user.compliance', $this->secondary_character->corporation->corporation_id))
            ->assertOk();

        $response->assertJsonCount(0, 'data');
    }

    /**
     * @test
     */
    public function user_with_permission_sees_user_compliance()
    {
        $this->createScopeSetting('user');

        $this->withoutMiddleware();

        $response = $this->actingAs($this->test_user)
            ->getJson(route('user.compliance', $this->secondary_character->corporation->corporation_id))
            ->assertOk()
        ;

        $response->assertJsonCount(1, 'data');

        $response = $this->actingAs($this->test_user)
            ->getJson(route('character.compliance', $this->secondary_character->corporation->corporation_id))
            ->assertOk();

        $response->assertJsonCount(0, 'data');

        $character_without_user = factory(CharacterInfo::class)->create();
        factory(CharacterAffiliation::class)->create([
            'character_id' => $character_without_user->character_id,
            'corporation_id' => $this->secondary_character->corporation->corporation_id
        ]);

        $response = $this->actingAs($this->test_user)
            ->getJson(route('missing.characters.compliance', $this->secondary_character->corporation->corporation_id))
            ->assertOk();

        $response->assertJsonCount(1, 'data');

    }

    private function createScopeSetting($type = 'default')
    {
        // create role
        $this->actingAs($this->superuser)
            ->followingRedirects()
            ->json('POST', route('acl.create'), ['name' => 'test']);

        // affiliate secondary user to role
        $role = Role::findByName('test');

        $response = $this->actingAs($this->superuser)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "permissions" => ["can open or close corporations for recruitment", "can accept or deny applications"],
                "allowed" => [
                    [
                        "corporation_id" => $this->secondary_character->corporation->corporation_id,
                        "id" => $this->secondary_character->corporation->corporation_id,
                        "name" => $this->secondary_character->corporation->name
                    ],
                ],
            ]);

        // give test user the role

        $response = $this->actingAs($this->superuser)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $role->id]), [
                "type" => 'manual',
                'affiliations' => [],
                'members' => [
                    [
                        'user_id' => $this->test_user->id,
                        'user' => $this->test_user
                    ],
                ]
            ])->assertOk();

        $this->assertTrue($this->test_user->refresh()->hasRole($role));

        $this->assertEmpty(SsoScopes::all());

        // Create sso scope

        // Make sure secondary character is missing the required scope
        $this->assertFalse(in_array('esi-assets.read_assets.v1', $this->secondary_character->refresh_token->scopes));

        // create scope setting

        SsoScopes::updateOrCreate([
            'morphable_id' => $this->secondary_character->corporation->corporation_id,
        ], [
            'selected_scopes' => ["esi-assets.read_assets.v1", "esi-universe.read_structures.v1"],
            'morphable_type' =>  CorporationInfo::class,
            'type' => $type
        ]);

       /* $response = $this->actingAs($this->superuser)
            ->followingRedirects()
            ->post(route('create.scopes'),
                [
                    'selectedEntities' => [
                        [
                            "corporation_id" => $this->secondary_character->corporation->corporation_id,
                            "id" => $this->secondary_character->corporation->corporation_id,
                            "name" => $this->secondary_character->corporation->name,
                            'category' => 'corporation'
                        ],
                    ],
                    'selectedScopes' => [
                        "esi-assets.read_assets.v1,esi-universe.read_structures.v1"
                    ],
                    'type' => $type
                ]
            )->assertOk();*/

        $this->assertCount(1, SsoScopes::all());

    }

    private function givePermissionsToTestUser(array $array)
    {

        foreach ($array as $string) {
            $permission = Permission::findOrCreate($string);

            $this->test_user->givePermissionTo($permission);
        }

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

}
