<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Services\Recruitment\ApplicationGroupService;

beforeEach(function () {
    assignPermissionToTestUser('superuser');
    cache()->flush();
});

/**
 * Gives test_user a second character and files a single-character application for each of its two
 * characters to $corp, tying them into one group. Returns the two applications.
 *
 * @return array{0: Application, 1: Application}
 */
function groupedApplications(CorporationInfo $corp): array
{
    $alt = CharacterUser::factory()->make();
    test()->test_user->characterUsers()->save($alt);

    $first = Application::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'applicationable_type' => CharacterInfo::class,
        'applicationable_id' => test()->test_character->character_id,
    ]);
    $second = Application::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'applicationable_type' => CharacterInfo::class,
        'applicationable_id' => $alt->character_id,
    ]);

    (new ApplicationGroupService)->create([(string) $first->id, (string) $second->id]);

    return [$first, $second];
}

it('collapses a multi-character group into one inbox row with its covered count', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Screen']);

    groupedApplications($corp);

    test()->actingAs(test()->test_user)
        ->get(route('recruitment.reviews'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('pending', 1)
            ->where('pending.0.covered_count', 2)
        );
});

it('shows every covered character when reviewing a grouped application', function () {
    $corp = CorporationInfo::factory()->create();

    [$first] = groupedApplications($corp);

    // The affiliation middleware is exercised elsewhere; here we assert the grouped character set.
    test()->withoutMiddleware();

    test()->actingAs(test()->test_user)
        ->get(route('get.application', $first->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Corporation/Recruitment/Application')
            ->has('recruit.characters', 2)
        );
});
