<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

it('lists open postings across corporations with their stages', function () {
    $corpA = CorporationInfo::factory()->create();
    $corpB = CorporationInfo::factory()->create();

    Enlistment::query()->create(['corporation_id' => $corpA->corporation_id, 'type' => 'character']);
    Enlistment::query()->create(['corporation_id' => $corpB->corporation_id, 'type' => 'user']);

    EnlistmentReviewRound::factory()->create(['corporation_id' => $corpA->corporation_id, 'position' => 0, 'label' => 'Screen']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corpA->corporation_id, 'position' => 1, 'label' => 'Final']);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.portal'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Recruitment/Portal/Index')
            ->has('postings', 2)
            ->has('postings.0.corporation.name')
            ->where('appliedCorporationIds', [])
        );
});

it('marks postings the user has already applied to', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);

    test()->test_user->application()->create(['corporation_id' => $corp->corporation_id]);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.portal'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('appliedCorporationIds', [$corp->corporation_id])
        );
});

it('submits an account-wide application from the portal', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);

    test()->actingAs(test()->test_user)
        ->post(route('recruitment.apply'), ['corporation_id' => $corp->corporation_id])
        ->assertRedirect();

    expect(Application::query()->where('corporation_id', $corp->corporation_id)->count())->toBe(1);
});
