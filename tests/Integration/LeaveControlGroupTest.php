<?php


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

uses(TestCase::class);

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    test()->role = Role::find($role->id);

    test()->secondary_user = User::factory()->create();
    test()->secondary_character = test()->secondary_user->characters->first();

});

test('user can leave himself', function () {

    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->test_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);


    // Second make test character member
    test()->role->activateMember(test()->test_user);

    test()->assertTrue(test()->test_user->hasRole(test()->role));

    assignPermissionToTestUser(['view access control']);

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id
        ]));

    test()->assertFalse(test()->test_user->refresh()->hasRole(test()->role));
});

test('user can kick other user as superuser', function () {

    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->secondary_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);

    // Second make secondary character member
    test()->role->activateMember(test()->secondary_user);

    test()->assertFalse(test()->test_user->hasRole(test()->role));
    test()->assertTrue(test()->secondary_user->hasRole(test()->role));

    assignPermissionToTestUser(['view access control', 'superuser']);

    test()->assertTrue(test()->test_user->can('superuser'));

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id
        ]));

    test()->assertFalse(test()->secondary_user->refresh()->hasRole(test()->role));
});

test('user can kick other user as moderator', function () {

    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->secondary_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);

    // Second make secondary character member
    test()->role->activateMember(test()->secondary_user);
    test()->assertTrue(test()->secondary_user->hasRole(test()->role));

    // Thirdly make primary character moderator
    test()->assertTrue(test()->role->moderators->isEmpty());
    test()->role->moderators()->create([
        'affiliatable_id' => test()->test_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
        'can_moderate' => true
    ]);
    test()->assertTrue(test()->role->refresh()->moderators->isNotEmpty());

    // Apparently a moderator does not need to be member
    test()->assertFalse(test()->test_user->hasRole(test()->role));

    assignPermissionToTestUser(['view access control']);
    test()->assertFalse(test()->test_user->can('superuser'));

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id
        ]));

    test()->assertFalse(test()->secondary_user->refresh()->hasRole(test()->role));
});

test('user can not kick other user as vanilla user', function () {

    // First create affiliation
    test()->role->acl_affiliations()->create([
        'affiliatable_id' => test()->secondary_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
    ]);

    // Second make secondary character member
    test()->role->activateMember(test()->secondary_user);
    test()->assertTrue(test()->secondary_user->hasRole(test()->role));

    assignPermissionToTestUser(['view access control']);
    test()->assertFalse(test()->test_user->can('superuser'));

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id
        ]));

    test()->assertEquals(403, $response->getStatusCode());

    test()->assertTrue(test()->secondary_user->refresh()->hasRole(test()->role));
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
