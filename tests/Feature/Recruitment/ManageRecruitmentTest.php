<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

beforeEach(function () {
    assignPermissionToTestUser('superuser');
    cache()->flush();
});

it('renders the manage workspace with the manageable postings', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.manage'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Recruitment/Manage/Index')
            ->has('postings', 1)
            ->has('controlGroups')
        );
});

it('opens a posting and seeds a default review stage', function () {
    $corp = CorporationInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->post(route('recruitment.posting.open'), ['corporation_id' => $corp->corporation_id, 'type' => 'user'])
        ->assertRedirect();

    expect(Enlistment::query()->find($corp->corporation_id))->not->toBeNull()
        ->and(EnlistmentReviewRound::query()->where('corporation_id', $corp->corporation_id)->pluck('label')->all())->toBe(['Open']);
});

it('replaces the review stages of a posting', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    test()->actingAs(test()->test_user)
        ->put(route('recruitment.posting.stages', $corp->corporation_id), [
            'stages' => [
                ['label' => 'Screen', 'role_id' => null],
                ['label' => 'Final', 'role_id' => null],
            ],
        ])
        ->assertRedirect();

    expect(EnlistmentReviewRound::query()->where('corporation_id', $corp->corporation_id)->orderBy('position')->pluck('label')->all())
        ->toBe(['Screen', 'Final']);
});

it('closes a posting and cascades its stages', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    test()->actingAs(test()->test_user)
        ->delete(route('recruitment.posting.close', $corp->corporation_id))
        ->assertRedirect();

    expect(Enlistment::query()->find($corp->corporation_id))->toBeNull()
        ->and(EnlistmentReviewRound::query()->where('corporation_id', $corp->corporation_id)->count())->toBe(0);
});
