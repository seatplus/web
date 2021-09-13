<?php


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class);

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::find($role->id);

    //dd(test()->test_character)

    /*CharacterAffiliation::factory()->create([
        'character_id' => test()->test_character->character_id
    ]);*/

    /*CharacterUser::factory()->create([
        'user_id' => test()->test_user->id,
        'character_id' => test()->test_character->character_id
    ]);*/

    test()->test_character = test()->test_character->refresh();
});

test('user can join waitlist', function () {


    test()->assertTrue(test()->role->acl_affiliations->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'on-request',
                'affiliations' => [
                    [
                        'type' => 'corporation',
                        'id' => test()->test_character->corporation->corporation_id
                    ]
                ],
                'members' => []
            ]
        ]);

    test()->assertFalse(test()->role->refresh()->acl_affiliations->isEmpty());

    test()->assertFalse(test()->test_user->hasRole(test()->role));

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.join'), [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id
        ]);


    test()->assertFalse(test()->test_user->hasRole(test()->role));

    test()->assertEquals(test()->test_user->id, test()->role->acl_members()->whereStatus('waitlist')->first()->user_id);
});

test('superuser can join immediately', function () {

    test()->assertTrue(test()->role->acl_affiliations->isEmpty());

    assignPermissionToTestUser(['superuser']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'on-request',
                'affiliations' => [
                    [
                        'type' => 'corporation',
                        'id' => test()->test_character->corporation->corporation_id
                    ]
                ],
                'members' => []
            ]
        ]);

    test()->assertFalse(test()->role->refresh()->acl_affiliations->isEmpty());

    test()->assertFalse(test()->test_user->roles->isNotEmpty());

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.join'), [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id
        ]);


    test()->assertTrue(test()->test_user->refresh()->hasRole(test()->role));

    test()->assertEquals(test()->test_user->id, test()->role->members()->first()->user_id);

});

// Helpers
function assignPermissionToTestUser(array $array)
{
    foreach ($array as $string) {
        $permission = Permission::findOrCreate($string);

        test()->test_user->givePermissionTo($permission);
    }

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
}
