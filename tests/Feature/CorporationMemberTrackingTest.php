<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;

beforeEach(function () {
    test()->test_character->roles()->update(['roles' => ['']]);
});

test('has dispatchable job', function () {
    test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertForbidden();

    test()->assignPermissionToTestUser('view member tracking');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->has('dispatchTransferObject'));
});

test('exposes a per-corporation member scroll prop', function () {
    $corporationId = test()->test_character->corporation->corporation_id;

    // Director corp role grants member-tracking access and includes the corp in the
    // affiliated ids (mirrors how a real director views their corp).
    CharacterRole::updateOrCreate(
        ['character_id' => test()->test_character->character_id],
        ['roles' => ['Director']],
    );

    CorporationMemberTracking::factory()
        ->count(3)
        ->create(['corporation_id' => $corporationId]);

    test()->actingAs(test()->test_user)
        ->get(route('corporation.member_tracking'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Corporation/MemberTracking/MemberTracking')
            ->has('corporations', 1)
            ->has("members_{$corporationId}.data", 3)
        );
});
