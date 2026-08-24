<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Employment\Employment;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Services\Recruitment\ApplicationGroupService;
use Seatplus\Web\Tests\TestCase;

beforeEach(function () {
    assignPermission($this->test_user, 'superuser');
    cache()->flush();
});

/**
 * Gives test_user a second character and files a single-character application for each of its two
 * characters to $corp, tying them into one group. Returns the two applications.
 *
 * @return array{0: Application, 1: Application}
 */
function groupedApplications(TestCase $case, CorporationInfo $corp): array
{
    $alt = CharacterUser::factory()->make();
    $case->test_user->characterUsers()->save($alt);

    $first = Application::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'applicationable_type' => CharacterInfo::class,
        'applicationable_id' => $case->test_character->character_id,
    ]);
    $second = Application::factory()->create([
        'corporation_id' => $corp->corporation_id,
        'applicationable_type' => CharacterInfo::class,
        'applicationable_id' => $alt->character_id,
    ]);

    (new ApplicationGroupService)->create([(string) $first->id, (string) $second->id]);

    // Refresh the in-memory user so its characters relation reflects the newly added alt.
    $case->test_user->load('characters');

    return [$first, $second];
}

it('collapses a multi-character group into one inbox row with its covered count', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Screen']);

    groupedApplications($this, $corp);

    $this->actingAs($this->test_user)
        ->get(route('recruitment.reviews'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('pending', 1)
            ->where('pending.0.covered_count', 2)
        );
});

it('shows every covered character when reviewing a grouped application', function () {
    $corp = CorporationInfo::factory()->create();

    [$first] = groupedApplications($this, $corp);

    // The affiliation middleware is exercised elsewhere; here we assert the grouped character set.
    $this->withoutMiddleware();

    $this->actingAs($this->test_user)
        ->get(route('get.application', $first->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Recruitment/Review/Application')
            ->has('recruit.characters', 2)
        );
});

it('hires every covered character when a grouped application is accepted', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);
    EnlistmentReviewRound::factory()->create(['corporation_id' => $corp->corporation_id, 'position' => 0, 'label' => 'Open']);

    [$first, $second] = groupedApplications($this, $corp);

    $this->withoutMiddleware();

    $this->actingAs($this->test_user)
        ->post(route('review.application', $first->id), ['decision' => 'accepted'])
        ->assertRedirect(route('recruitment.reviews'));

    // Single round → final acceptance hires each covered character as its own Employment.
    expect($first->refresh()->status)->toBe('accepted')
        ->and($second->refresh()->status)->toBe('accepted')
        ->and(Employment::query()->where('corporation_id', $corp->corporation_id)->count())->toBe(2);
});

it('withdraws the whole group when one member is withdrawn', function () {
    $corp = CorporationInfo::factory()->create();

    [$first, $second] = groupedApplications($this, $corp);

    $this->actingAs($this->test_user)
        ->delete(route('recruitment.withdraw', $first->id))
        ->assertRedirect();

    expect(Application::query()->whereKey($first->id)->exists())->toBeFalse()
        ->and(Application::query()->whereKey($second->id)->exists())->toBeFalse()
        ->and(ApplicationGroupMember::query()->count())->toBe(0);
});

it('collapses the applicant\'s own group into a single portal entry listing its characters', function () {
    $corp = CorporationInfo::factory()->create();
    Enlistment::query()->create(['corporation_id' => $corp->corporation_id, 'type' => 'character']);

    groupedApplications($this, $corp);

    $this->actingAs($this->test_user)
        ->get(route('recruitment.portal'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('myApplications', 1)
            ->has('myApplications.0.covered_characters', 2)
        );
});
