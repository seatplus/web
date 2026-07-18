<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

beforeEach(function () {
    assignPermissionToTestUser('superuser');
    cache()->flush();
});

it('lists open applications with their current stage', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Screen']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 1, 'label' => 'Final']);

    Application::factory()->create(['corporation_id' => $corp->corporation_id]);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.reviews'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Recruitment/Reviews/Index')
            ->has('pending', 1)
            ->where('pending.0.total_stages', 2)
            ->where('pending.0.stage.position', 0)
            ->where('pending.0.stage.label', 'Screen')
        );
});

it('excludes applications that are no longer open', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    Application::factory()->create(['corporation_id' => $corp->corporation_id, 'status' => 'accepted']);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.reviews'))
        ->assertInertia(fn (Assert $page) => $page->has('pending', 0));
});
