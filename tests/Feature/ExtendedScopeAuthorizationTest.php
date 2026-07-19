<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\Affiliations\GetCorporationMemberComplianceAffiliatedIdsService;

beforeEach(function () {
    // createRoleViaHttp() defaults its actor to test()->superuser.
    test()->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });
});

// GetCorporationMemberComplianceAffiliatedIdsService resolves the character ids a reviewer may see
// through the CheckAuthorizationWithExtendedScope middleware, which gates the character Asset,
// Contract and Corporation-History routes. (The former Member Compliance UI that also used it is gone.)
it('affiliates member-compliance ids for extended scope', function () {
    Event::fake();

    // create a user with two characters
    $user = User::factory()->create();
    $secondary_character = CharacterUser::factory()->make();

    $user->characterUsers()->save($secondary_character);

    expect($user->characters->count())->toEqual(2);

    $first_character = $user->characters()->with('corporation')->first();
    $second_character = $user->characters()->with('corporation')->get()->last();

    expect($first_character->character_id)->not()->toEqual($second_character->character_id);

    // create role with affiliation and permission via HTTP
    createRoleViaHttp(
        roleName: faker()->name(),
        affiliations: [
            [
                'entity_id' => $first_character->character_id,
                'entity_type' => 'character',
                'affiliation_type' => 'allowed',
            ],
        ],
        member: test()->test_user,
        permissions: ['member compliance: review user'],
    );

    // check if test user has permission
    expect(test()->test_user->refresh()->can('member compliance: review user'))->toBeTrue();

    // create sso scope
    SsoScopes::factory()->create([
        'morphable_type' => CorporationInfo::class,
        'morphable_id' => $first_character->corporation->corporation_id,
        'type' => 'user',
        'selected_scopes' => collect(['esi-alliances.read_corporations.v1'])->toJson(),
    ]);

    \Pest\Laravel\actingAs(test()->test_user);
    $affiliated_ids = GetCorporationMemberComplianceAffiliatedIdsService::make()->getQuery()->get();
    $affiliated_character_ids = $affiliated_ids->pluck('affiliated_id');

    expect($affiliated_ids)->toHaveCount(2)
        ->and($affiliated_character_ids)->toContain($first_character->character_id)
        ->and($affiliated_character_ids)->toContain($second_character->character_id);

    // The reviewer can reach the character skills page via extended-scope authorization.
    $response = test()->actingAs(test()->test_user)->get(route('character.skills'));

    $response->assertOk();
});
