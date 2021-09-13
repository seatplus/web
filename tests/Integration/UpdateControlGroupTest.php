<?php


use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

test('on can update role type', function () {

    //dd(test()->test_user->hasRole('test'));

    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'automatic',
                'affiliations' => [],
                'members' => []
            ]
        ]);

    expect(test()->role->fresh()->type)->toEqual('automatic');
});

test('manual control group adds member', function () {
    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

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

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();
});

test('manual control group removes member', function () {
    test()->role->activateMember(test()->test_user);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            "acl" => [
                "type" => 'manual',
                'affiliations' => [],
                'members' => []
            ]
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('automatic control group adds affiliation', function () {

    expect(test()->role->acl_affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

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

    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeFalse();
});

test('automatic control group removes affiliation', function () {

    expect(test()->role->acl_affiliations->isEmpty())->toBeTrue();

    test()->role->acl_affiliations()->create([
    'affiliatable_id' => CorporationInfo::factory()->make()->corporation_id,
    'affiliatable_type' => CorporationInfo::class
    ]);

    test()->assertFalse(test()->role->refresh() ->acl_affiliations->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
           "acl" => [
               "type" => 'automatic',
               'affiliations' => [],
               'members' => []
           ]
        ]);

    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeTrue();
});

test('on request control group adds and removes moderators', function () {

    expect(test()->role->moderators->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

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
    expect(test()->role->refresh()->moderators->isNotEmpty())->toBeTrue();
    expect(test()->role->refresh()->isModerator(test()->test_user))->toBeTrue();

    // assert that no affiliations has been created
    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeTrue();

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
