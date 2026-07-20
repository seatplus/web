<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;
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
            ->has('myApplications', 0)
        );
});

it('sorts the applicant\'s current corporation to the bottom of the portal', function () {
    $ownCorp = test()->test_character->corporation;
    $otherCorp = CorporationInfo::factory()->create();

    // Own-corp posting is created first, so only the sort can push it below the other corp.
    Enlistment::query()->create(['corporation_id' => $ownCorp->corporation_id, 'type' => 'user']);
    Enlistment::query()->create(['corporation_id' => $otherCorp->corporation_id, 'type' => 'user']);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.portal'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('postings', 2)
            ->where('postings.0.corporation_id', $otherCorp->corporation_id)
            ->where('postings.1.corporation_id', $ownCorp->corporation_id)
        );
});

it('surfaces the user\'s own application with its progress', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    test()->test_user->application()->create(['corporation_id' => $corp->corporation_id]);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.portal'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('myApplications', 1)
            ->where('myApplications.0.corporation.corporation_id', $corp->corporation_id)
            ->where('myApplications.0.status', 'open')
            ->where('myApplications.0.total_stages', 1)
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

it('submits a multi-character application covering a subset of the account', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);

    // test_user starts with one character; give it two more so we can apply with a subset.
    $alt1 = CharacterUser::factory()->make();
    $alt2 = CharacterUser::factory()->make();
    test()->test_user->characterUsers()->save($alt1);
    test()->test_user->characterUsers()->save($alt2);
    // The base TestCase already loaded ->characters (1 char) on this instance; a real request resolves
    // the auth user fresh, so refresh the relation to reflect all three characters.
    test()->test_user->load('characters');

    $covered = [test()->test_character->character_id, $alt1->character_id];

    test()->actingAs(test()->test_user)
        ->post(route('recruitment.apply'), [
            'corporation_id' => $corp->corporation_id,
            'character_ids' => $covered,
        ])
        ->assertRedirect();

    // One CharacterInfo application per covered character; the uncovered third stays untouched.
    expect(Application::query()->where('applicationable_type', CharacterInfo::class)->whereIn('applicationable_id', $covered)->count())->toBe(2)
        ->and(Application::query()->where('applicationable_id', $alt2->character_id)->exists())->toBeFalse();

    // Both applications are tied under a single group.
    expect(ApplicationGroupMember::query()->count())->toBe(2)
        ->and(ApplicationGroupMember::query()->distinct()->count('group_id'))->toBe(1);
});

it('withdraws the user\'s own application', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'user']);
    $application = test()->test_user->application()->create(['corporation_id' => $corp->corporation_id]);

    test()->actingAs(test()->test_user)
        ->delete(route('recruitment.withdraw', $application->id))
        ->assertRedirect();

    expect(Application::query()->whereKey($application->id)->exists())->toBeFalse();
});
