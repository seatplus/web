<?php

use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\User;
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

    $applicant = Event::fakeFor(fn () => User::factory()->create());
    Application::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'applicationable_type' => User::class,
        'applicationable_id' => $applicant->getKey(),
    ]);

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

it('moves settled applications out of the queue and into the history list', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    $applicant = Event::fakeFor(fn () => User::factory()->create());
    Application::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'applicationable_type' => User::class,
        'applicationable_id' => $applicant->getKey(),
        'status' => 'accepted',
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.reviews'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('pending', 0)
            ->has('history', 1)
            ->where('history.0.status', 'accepted')
            // A decision ends access to the applicant's data, so a history row carries the decision
            // record and no link back into the inspection tabs (#1662).
            ->missing('history.0.review_url')
        );
});
