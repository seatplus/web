<?php


namespace Seatplus\Web\Tests\Integration;


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class LeaveControlGroupTest extends TestCase
{

    private Role $role;

    private CharacterInfo $secondary_character;

    private User $secondary_user;

    public function setUp(): void
    {

        parent::setUp();

        Queue::fake();

        $role = Role::create(['name' => 'test', 'type' => 'on-request']);
        $this->role = Role::find($role->id);

        $this->secondary_user = User::factory()->create();
        $this->secondary_character = $this->secondary_user->characters->first();

    }


    /** @test */
    public function user_can_leave_himself()
    {

        // First create affiliation
        $this->role->acl_affiliations()->create([
            'affiliatable_id' => $this->test_character->character_id,
            'affiliatable_type' => CharacterInfo::class,
        ]);


        // Second make test character member
        $this->role->activateMember($this->test_user);

        $this->assertTrue($this->test_user->hasRole($this->role));

        $this->assignPermissionToTestUser(['view access control']);

        $response = $this->actingAs($this->test_user)
            ->delete(route('acl.leave', [
                'user_id' => $this->test_user->id,
                'role_id' => $this->role->id
            ]));

        $this->assertFalse($this->test_user->refresh()->hasRole($this->role));
    }

    /** @test */
    public function user_can_kick_other_user_as_superuser()
    {

        // First create affiliation
        $this->role->acl_affiliations()->create([
            'affiliatable_id' => $this->secondary_character->character_id,
            'affiliatable_type' => CharacterInfo::class,
        ]);

        // Second make secondary character member
        $this->role->activateMember($this->secondary_user);

        $this->assertFalse($this->test_user->hasRole($this->role));
        $this->assertTrue($this->secondary_user->hasRole($this->role));

        $this->assignPermissionToTestUser(['view access control', 'superuser']);

        $this->assertTrue($this->test_user->can('superuser'));

        $response = $this->actingAs($this->test_user)
            ->delete(route('acl.leave', [
                'user_id' => $this->secondary_user->id,
                'role_id' => $this->role->id
            ]));

        $this->assertFalse($this->secondary_user->refresh()->hasRole($this->role));
    }

    /** @test */
    public function user_can_kick_other_user_as_moderator()
    {

        // First create affiliation
        $this->role->acl_affiliations()->create([
            'affiliatable_id' => $this->secondary_character->character_id,
            'affiliatable_type' => CharacterInfo::class,
        ]);

        // Second make secondary character member
        $this->role->activateMember($this->secondary_user);
        $this->assertTrue($this->secondary_user->hasRole($this->role));

        // Thirdly make primary character moderator
        $this->assertTrue($this->role->moderators->isEmpty());
        $this->role->moderators()->create([
            'affiliatable_id' => $this->test_character->character_id,
            'affiliatable_type' => CharacterInfo::class,
            'can_moderate' => true
        ]);
        $this->assertTrue($this->role->refresh()->moderators->isNotEmpty());

        // Apparently a moderator does not need to be member
        $this->assertFalse($this->test_user->hasRole($this->role));

        $this->assignPermissionToTestUser(['view access control']);
        $this->assertFalse($this->test_user->can('superuser'));

        $response = $this->actingAs($this->test_user)
            ->delete(route('acl.leave', [
                'user_id' => $this->secondary_user->id,
                'role_id' => $this->role->id
            ]));

        $this->assertFalse($this->secondary_user->refresh()->hasRole($this->role));
    }

    /** @test */
    public function user_can_not_kick_other_user_as_vanilla_user()
    {

        // First create affiliation
        $this->role->acl_affiliations()->create([
            'affiliatable_id' => $this->secondary_character->character_id,
            'affiliatable_type' => CharacterInfo::class,
        ]);

        // Second make secondary character member
        $this->role->activateMember($this->secondary_user);
        $this->assertTrue($this->secondary_user->hasRole($this->role));

        $this->assignPermissionToTestUser(['view access control']);
        $this->assertFalse($this->test_user->can('superuser'));

        $response = $this->actingAs($this->test_user)
            ->delete(route('acl.leave', [
                'user_id' => $this->secondary_user->id,
                'role_id' => $this->role->id
            ]));

        $this->assertEquals(403, $response->getStatusCode());

        $this->assertTrue($this->secondary_user->refresh()->hasRole($this->role));
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
