<?php


use Inertia\Testing\Assert;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    test()->secondary_user = Event::fakeFor(function () {
        return User::factory()->create();
    });

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    test()->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();

        $permission = Permission::findOrCreate('superuser');

        $user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        app()->make(PermissionRegistrar::class)->registerPermissions();

        return $user;
    });

    test()->secondary_character = test()->secondary_user->characters->first();
});

test('user without permission fails to see compliance', function () {

    if(test()->test_user->can('superuser')) {
        test()->test_user->removeRole('superuser');

        // now re-register all the roles and permissions
        app()->make(PermissionRegistrar::class)->registerPermissions();
    }

    $response = test()->actingAs(test()->secondary_user)
        ->get(route('corporation.member_compliance'))
        ->assertForbidden();

});

test('user with permission sees component', function () {
    if(test()->test_user->can('superuser')) {
        test()->test_user->removeRole('superuser');

        // now re-register all the roles and permissions
        app()->make(PermissionRegistrar::class)->registerPermissions();
    }

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.member_compliance'))
        ->assertForbidden();

    assignPermissionToTestUser(['view member compliance']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.member_compliance'))
        ->assertOk();

    $response->assertInertia( fn (Assert $page) => $page->component('Corporation/MemberCompliance/MemberCompliance'));
});

test('user with permission sees character compliance', function () {
    createScopeSetting();

    test()->withoutMiddleware();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('character.compliance', test()->secondary_character->corporation->corporation_id))
        ->assertOk();

    $response->assertJsonCount(1, 'data');

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('user.compliance', test()->secondary_character->corporation->corporation_id))
        ->assertOk();

    $response->assertJsonCount(0, 'data');
});

test('user with permission sees user compliance', function () {
    createScopeSetting('user');

    test()->withoutMiddleware();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('user.compliance', test()->secondary_character->corporation->corporation_id))
        ->assertOk();

    $response->assertJsonCount(1, 'data');

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('character.compliance', test()->secondary_character->corporation->corporation_id))
        ->assertOk();

    $response->assertJsonCount(0, 'data');

    $character_without_user = CharacterInfo::factory()->create();

    $character_affiliation = $character_without_user->character_affiliation;
    $character_affiliation->corporation_id = test()->secondary_character->corporation->corporation_id;
    $character_affiliation->alliance_id = test()->secondary_character->alliance?->alliance_id;
    $character_affiliation->save();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('missing.characters.compliance', test()->secondary_character->corporation->corporation_id))
        ->assertOk();

    $response->assertJsonCount(1, 'data');

});

test('director user without permission can access index', function () {
    // 1. non director can't access the compliance index
    $non_director = Event::fakeFor( function() {
        $user = User::factory()->create();

        $roles = $user->characters->first()->roles;
        $roles->roles = ['Contract_Manager'];
        $roles->save();

        return $user->refresh();
    });

    test()->actingAs($non_director)
        ->get(route('corporation.member_compliance'))
        ->assertForbidden();

    // 2. director can access the compliance index

    $director = Event::fakeFor( function() {
        $user = User::factory()->create();

        $roles = $user->characters->first()->roles;
        $roles->roles = ['Director'];
        $roles->save();

        return $user->refresh();
    });

    test()->actingAs($director)
        ->get(route('corporation.member_compliance'))
        ->assertOk();


});

// Helpers
function createScopeSetting($type = 'default')
{
    // create role
    test()->actingAs(test()->superuser)
        ->followingRedirects()
        ->json('POST', route('acl.create'), ['name' => 'test']);

    // affiliate secondary user to role
    $role = Role::findByName('test');

    $response = test()->actingAs(test()->superuser)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            "permissions" => ["can open or close corporations for recruitment", "can accept or deny applications"],
            "allowed" => [
                [
                    "corporation_id" => test()->secondary_character->corporation->corporation_id,
                    "id" => test()->secondary_character->corporation->corporation_id,
                    "name" => test()->secondary_character->corporation->name
                ],
            ],
        ]);

    // give test user the role

    $response = test()->actingAs(test()->superuser)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => $role->id]), [
            'acl' => [
                "type" => 'manual',
                'affiliations' => [],
                'members' => [
                    [
                        'id' => test()->test_user->id,
                        'user' => test()->test_user
                    ],
                ]
            ]
        ])->assertOk();

    expect(test()->test_user->refresh()->hasRole($role))->toBeTrue();

    expect(SsoScopes::all())->toBeEmpty();

    // Create sso scope

    // Make sure secondary character is missing the required scope
    expect(in_array('esi-assets.read_assets.v1', test()->secondary_character->refresh_token->scopes))->toBeFalse();

    // create scope setting

    SsoScopes::updateOrCreate([
        'morphable_id' => test()->secondary_character->corporation->corporation_id,
    ], [
        'selected_scopes' => ["esi-assets.read_assets.v1", "esi-universe.read_structures.v1"],
        'morphable_type' =>  CorporationInfo::class,
        'type' => $type
    ]);

   /* $response = test()->actingAs(test()->superuser)
        ->followingRedirects()
        ->post(route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        "corporation_id" => test()->secondary_character->corporation->corporation_id,
                        "id" => test()->secondary_character->corporation->corporation_id,
                        "name" => test()->secondary_character->corporation->name,
                        'category' => 'corporation'
                    ],
                ],
                'selectedScopes' => [
                    "esi-assets.read_assets.v1,esi-universe.read_structures.v1"
                ],
                'type' => $type
            ]
        )->assertOk();*/

    expect(SsoScopes::all())->toHaveCount(1);

}
