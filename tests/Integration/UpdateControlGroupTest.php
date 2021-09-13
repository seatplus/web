<?php


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class);

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

test('on can update role type', function () {

    //dd(test()->test_user->hasRole('test'));

    test()->assertFalse(test()->test_user->hasRole('test'));

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'automatic',
                'affiliations' => [],
                'members' => []
            ]
        ]);

    test()->assertEquals('automatic', test()->role->fresh()->type);
});

test('manual control group adds member', function () {
    test()->assertFalse(test()->test_user->hasRole('test'));

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'manual',
                'affiliations' => [],
                'members' => [
                    [
                        'id' => test()->test_user->id,
                        'user' => test()->test_user
                    ],
                ]
            ]
        ]);

    test()->assertTrue(test()->test_user->refresh()->hasRole(test()->role));
});

test('manual control group removes member', function () {
    test()->role->activateMember(test()->test_user);

    test()->assertTrue(test()->test_user->refresh()->hasRole(test()->role));

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'manual',
                'affiliations' => [],
                'members' => []
            ]
        ]);

    test()->assertFalse(test()->test_user->refresh()->hasRole(test()->role));
});

test('automatic control group adds affiliation', function () {

    test()->assertTrue(test()->role->acl_affiliations->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
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

    test()->assertFalse(test()->role->refresh()->acl_affiliations->isEmpty());
});

test('automatic control group removes affiliation', function () {

    test()->assertTrue(test()->role->acl_affiliations->isEmpty());

    test()->role->acl_affiliations()->create([
    'affiliatable_id' => CorporationInfo::factory()->make()->corporation_id,
    'affiliatable_type' => CorporationInfo::class
    ]);

    test()->assertFalse(test()->role->refresh() ->acl_affiliations->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
           "acl" => [
               "type" => 'automatic',
               'affiliations' => [],
               'members' => []
           ]
        ]);

    test()->assertTrue(test()->role->refresh()->acl_affiliations->isEmpty());
});

test('on request control group adds and removes moderators', function () {

    test()->assertTrue(test()->role->moderators->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    test()->assertEquals('manual', test()->role->type);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'on-request',
                'moderators' => [
                    [
                        'id' => test()->test_user->id
                    ]
                ]
            ]
        ]);

    // Test if test user is moderator
    test()->assertTrue(test()->role->refresh()->moderators->isNotEmpty());
    test()->assertTrue(test()->role->refresh()->isModerator(test()->test_user));

    // assert that no affiliations has been created
    test()->assertTrue(test()->role->refresh()->acl_affiliations->isEmpty());

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
