<?php


use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\Affiliations\GetCorporationMemberComplianceAffiliatedIdsService;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    test()->secondary_user = Event::fakeFor(fn () => User::factory()->create());

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
    if (test()->test_user->can('superuser')) {
        test()->test_user->removeRole('superuser');

        // now re-register all the roles and permissions
        app()->make(PermissionRegistrar::class)->registerPermissions();
    }

    $response = test()->actingAs(test()->secondary_user)
        ->get(route('corporation.member_compliance'))
        ->assertUnauthorized();
});

test('user with permission sees component', function () {
    if (test()->test_user->can('superuser')) {
        test()->test_user->removeRole('superuser');

        // now re-register all the roles and permissions
        app()->make(PermissionRegistrar::class)->registerPermissions();
    }

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.member_compliance'))
        ->assertUnauthorized();

    assignPermissionToTestUser(['view member compliance']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.member_compliance'))
        ->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->component('Corporation/MemberCompliance/MemberCompliance'));
});

test('user with permission sees default compliance', function () {
    createScopeSetting();

    test()->withoutMiddleware();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('corporation.compliance', [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'type' => 'default',
        ]))
        ->assertOk();

    $response->assertJsonCount(1, 'data');
});

it('is possible to search for a character', function () {
    createScopeSetting();

    test()->withoutMiddleware();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('corporation.compliance', [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'type' => 'default',
            'search' => substr(test()->secondary_character->name, 5),
        ]))
        ->assertOk();

    $response->assertJsonCount(1, 'data');
});

test('user with permission sees user compliance', function () {
    createScopeSetting(['view member compliance'], 'user');

    test()->withoutMiddleware();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('corporation.compliance', [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'type' => 'user',
        ]));

    $response->assertJsonCount(1, 'data');

    expect(test()->test_user->characters)->toHaveCount(1);

    $response->assertJsonFragment(['count_total' => 1]);

    CharacterUser::query()->where('character_id', test()->secondary_character->character_id)
        ->update(['user_id' => test()->test_user->id]);

    expect(test()->test_user->refresh()->characters)->toHaveCount(2);

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('corporation.compliance', [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'type' => 'user',
        ]));

    $response->assertJsonFragment(['count_total' => 2]);
});

test('non director can not access the compliance index', function () {
    // 1. non director can't access the compliance index
    $non_director = Event::fakeFor(function () {
        $user = User::factory()->create();

        $roles = $user->characters->first()->roles;
        $roles->roles = ['Contract_Manager'];
        $roles->save();

        return $user->refresh();
    });

    test()->actingAs($non_director)
        ->get(route('corporation.member_compliance'))
        ->assertUnauthorized();
});

test('director user without permission can access index', function () {
    $director = Event::fakeFor(function () {
        $user = User::factory()->create();

        $roles = $user->characters->first()->roles;
        $roles->roles = ['Director'];
        $roles->save();

        return $user->refresh();
    });

    $response = test()->actingAs($director)
        ->get(route('corporation.member_compliance'))
        ->assertOk();

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Corporation/MemberCompliance/MemberCompliance')
    );
});

test('director user without permission can review its corp members', function () {
    //createScopeSetting();

    // 1. make sure user ist not superuser
    expect(test()->test_user->can('superuser'))->toBeFalse();

    // 2. director can access the compliance index
    $director = Event::fakeFor(function () {
        $user = test()->test_user;

        $roles = $user->characters->first()->roles;
        $roles->roles = ['Director'];
        $roles->save();

        return $user->refresh();
    });

    expect(test()->test_character->roles->first()->roles)->toContain('Director');

    // 3. setup ssoScope
    SsoScopes::updateOrCreate([
        'morphable_id' => test()->test_character->corporation->corporation_id,
    ], [
        'selected_scopes' => test()->test_character->refresh_token->scopes,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
    ]);

    CorporationInfo::query()
        ->has('ssoScopes')
        ->orHas('alliance.ssoScopes')
        ->select('name', 'corporation_id')
        ->addSelect(['type' => SsoScopes::whereColumn('morphable_id', 'corporation_id')->limit(1)->select('type')])
        ->get();

    $response = test()->actingAs($director)
        ->get(route('corporation.member_compliance'))
        ->assertOk();

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Corporation/MemberCompliance/MemberCompliance')
        ->has('corporations', 1)
    );
});

it('enables superuser to review corporation member', function () {
    createScopeSetting();

    expect(test()->superuser)
        ->can('superuser')->toBeTrue();

    $response = test()->actingAs(test()->superuser)
        ->getJson(route('corporation.review.user', [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'user' => test()->test_user->id,
        ]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Corporation/MemberCompliance/ReviewUser'));
});

it('enables with review permission to review corporation member', function () {
    createScopeSetting(['view member compliance', 'member compliance: review user']);

    expect(test()->test_user)
        ->can('superuser')->toBeFalse()
        ->can('member compliance: review user')->toBeTrue()
        ->can('view member compliance')->toBeTrue();

    $response = test()->actingAs(test()->test_user)
        ->getJson(route('corporation.review.user', [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'user' => test()->test_user->id,
        ]))->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Corporation/MemberCompliance/ReviewUser'));
});

it('allows user with review permission to review corporation member', function () {
    \Illuminate\Support\Facades\Event::fake();

    // create a user with two characters
    $user = User::factory()->create();
    $secondary_character = CharacterUser::factory()->make();

    $user->character_users()->save($secondary_character);

    expect($user->characters->count())->toEqual(2);

    $first_character = $user->characters->first();
    $second_character = $user->characters->last();

    expect($first_character->character_id)->not()->toEqual($second_character->character_id);

    // create role
    $role = Role::create(['name' => faker()->name]);
    $permission = Permission::create(['name' => 'member compliance: review user']);

    $role->givePermissionTo($permission);
    $role->activateMember(test()->test_user);

    // check if test user has permission
    expect(test()->test_user->can('member compliance: review user'))->toBeTrue();

    // create affiliation
    $role->affiliations()->create([
        'affiliatable_id' => $first_character->character_id,
        'affiliatable_type' => \Seatplus\Eveapi\Models\Character\CharacterInfo::class,
        'type' => 'allowed',
    ]);

    // create sso scope
    $sso_scope = SsoScopes::factory()->create([
        'morphable_type' => \Seatplus\Eveapi\Models\Corporation\CorporationInfo::class,
        'morphable_id' => $first_character->corporation->corporation_id,
        'type' => 'user',
        'selected_scopes' => collect(['esi-alliances.read_corporations.v1'])->toJson(),
    ]);

    \Pest\Laravel\actingAs(test()->test_user);
    $affiliated_ids = GetCorporationMemberComplianceAffiliatedIdsService::make()->getQuery()->get();

    expect($affiliated_ids)->toHaveCount(2)
        ->and($affiliated_ids->first()->affiliated_id)->toEqual($first_character->character_id)
        ->and($affiliated_ids->last()->affiliated_id)->toEqual($second_character->character_id);

    $response = test()->actingAs(test()->test_user)->get(route('get.character.skills', [
        'character_id' => $second_character->character_id,
    ]));

    $response->assertOk();
});

// Helpers
function createScopeSetting(array $permissons = [], $type = 'default')
{
    // create role
    test()->actingAs(test()->superuser)
        ->followingRedirects()
        ->json('POST', route('acl.create'), ['name' => 'test']);

    // affiliate secondary user to role
    $role = Role::findByName('test');

    test()->actingAs(test()->superuser)
        ->json('POST', route('acl.update', ['role_id' => $role->id]), [
            "affiliations" => [
                [
                    "category" => 'corporation',
                    "id" => test()->secondary_character->corporation->corporation_id,
                    "type" => "allowed",
                ],
            ],
            'permissions' => $permissons,
            "roleName" => $role->name,
        ])
        ->assertRedirect();

    expect($role->affiliated_ids)->toContain(test()->secondary_character->corporation->corporation_id);

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
                        'user' => test()->test_user,
                    ],
                ],
            ],
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
        'morphable_type' => CorporationInfo::class,
        'type' => $type,
    ]);

    expect(SsoScopes::all())->toHaveCount(1);
}
