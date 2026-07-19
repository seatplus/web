<?php

use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Web\Models\Employment\Employment;

beforeEach(function () {
    assignPermissionToTestUser('superuser');
    cache()->flush();
});

it('lists every authorized corporation, even without SSO scopes configured', function () {
    $corp = test()->test_character->corporation;

    test()->actingAs(test()->test_user)
        ->get(route('employment.observe'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Employment/Index')
            ->where('corporations', fn (Collection $corporations) => $corporations->pluck('corporation_id')->contains($corp->corporation_id))
        );
});

it('returns a corporation\'s members with compliance, activity and employment status', function () {
    $corp = test()->test_character->corporation;

    CorporationMemberTracking::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'character_id' => test()->test_character->character_id,
    ]);

    Employment::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'subject_type' => User::class,
        'subject_id' => test()->test_user->getKey(),
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('employment.observe.corporation', $corp->corporation_id))
        ->assertOk()
        ->assertJsonFragment(['id' => test()->test_user->getKey()])
        ->assertJsonFragment(['employment_status' => 'active']);
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
