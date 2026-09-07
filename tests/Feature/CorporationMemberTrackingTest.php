<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;

beforeEach(function () {
    $this->test_character->roles()->update(['roles' => ['']]);
});

test('has dispatchable job', function () {
    $this->actingAs($this->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertForbidden();

    assignPermission($this->test_user, 'view member tracking');

    $response = $this->actingAs($this->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->has('dispatchTransferObject'));
});

test('exposes a per-corporation member scroll prop', function () {
    $corporationId = $this->test_character->corporation->corporation_id;

    // Director corp role grants member-tracking access and includes the corp in the
    // affiliated ids (mirrors how a real director views their corp).
    CharacterRole::updateOrCreate(
        ['character_id' => $this->test_character->character_id],
        ['roles' => ['Director']],
    );

    CorporationMemberTracking::factory()
        ->count(3)
        ->create(['corporation_id' => $corporationId]);

    $this->actingAs($this->test_user)
        ->get(route('corporation.member_tracking'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Corporation/MemberTracking/MemberTracking')
            ->has('corporations', 1)
            ->has("members_{$corporationId}.data", 3)
        );
});
