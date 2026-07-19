<?php

use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Models\Employment\Employment;

beforeEach(function () {
    assignPermissionToTestUser('superuser');
    cache()->flush();
});

it('lists only corporations that have SSO scopes configured', function () {
    $configured = test()->test_character->corporation;
    SsoScopes::factory()->create([
        'morphable_id' => $configured->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
    ]);

    // Authorized (superuser) but no scopes → nothing to be compliant against → excluded.
    $unconfigured = CorporationInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->get(route('employment.observe'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Employment/Index')
            ->where('corporations', fn (Collection $corporations) => $corporations->pluck('corporation_id')->contains($configured->corporation_id)
                && ! $corporations->pluck('corporation_id')->contains($unconfigured->corporation_id))
        );
});

it('exposes a corporation\'s members with compliance, activity and employment status as a deferred prop', function () {
    $corp = test()->test_character->corporation;

    // The corp must be observable (has SSO scopes) to appear on the index and get its members computed.
    // Pass a real array (the eveapi factory default is a JSON string, which the array cast would double-encode).
    SsoScopes::factory()->create([
        'morphable_id' => $corp->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
        'selected_scopes' => ['esi-assets.read_assets.v1'],
    ]);

    CorporationMemberTracking::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'character_id' => test()->test_character->character_id,
    ]);

    Employment::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'subject_type' => User::class,
        'subject_id' => test()->test_user->getKey(),
    ]);

    // members is a deferred prop keyed by corporation_id — request it via a partial Inertia reload.
    test()->actingAs(test()->test_user)
        ->get(route('employment.observe'), [
            'X-Inertia' => 'true',
            'X-Inertia-Partial-Component' => 'Employment/Index',
            'X-Inertia-Partial-Data' => 'members',
        ])
        ->assertOk()
        ->assertJsonPath("props.members.{$corp->corporation_id}.0.id", test()->test_user->getKey())
        ->assertJsonPath("props.members.{$corp->corporation_id}.0.employment_status", 'active');
});

it('inspects a member with the shared review tabs', function () {
    $corp = test()->test_character->corporation;

    test()->actingAs(test()->test_user)
        ->get(route('employment.observe.member', [$corp->corporation_id, test()->test_user->getKey()]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Employment/Inspect')
            ->has('recruit')
            ->has('watchlist')
            ->has('targetCorporation')
        );
});
