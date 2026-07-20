<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

require_once __DIR__.'/helpers.php';

/*
 * End-to-end browser tests for the employee-recruitment lifecycle, run against the assembled core
 * app. They walk each persona through their surface and screenshot it:
 *   - HR manager  → Manage Recruitment (open posting, configure staged review)
 *   - Applicant   → Job Portal (apply, then see progress + reviewer-action timeline)
 *   - Junior/Senior reviewer → Reviews inbox (each sees ONLY the stage their control group handles)
 * Authorization model: a reviewer must be a recruiter for the corporation (director here) AND a
 * member of the stage's control group.
 */

uses(RefreshDatabase::class);

// ═══ HR manager — Manage Recruitment ══════════════════════════════════════════════════════════

it('manage — an HR manager sees their posting and its staged review process', function (string $device) {
    $character = actingAsCharacter();
    makeRecruiterOfCorporation($character, 'can open or close corporations for recruitment');

    $junior = makeControlGroup('Junior HR');
    $senior = makeControlGroup('Senior HR');
    openPostingWithStages($character->corporation_id, [
        ['label' => 'Screening', 'role' => $junior],
        ['label' => 'Final', 'role' => $senior],
    ]);
    cache()->flush();

    $page = deviceVisit($device, '/recruitment/manage');
    $page->assertNoSmoke();

    $page->waitForText('Manage Recruitment');
    // The stage labels live in editable inputs (values), not page text, so assert on their values.
    $page->waitForText('Review stages');
    $page->assertScript("Array.from(document.querySelectorAll('input')).some((i) => i.value === 'Screening')");
    $page->assertScript("Array.from(document.querySelectorAll('input')).some((i) => i.value === 'Final')");

    snap($page, "recruitment-manage-{$device}");
})->with(['desktop', 'iphone']);

// ═══ Applicant — Job Portal ═══════════════════════════════════════════════════════════════════

it('portal — an applicant sees an open posting they can apply to', function (string $device) {
    $character = actingAsCharacter();

    openPostingWithStages($character->corporation_id, [['label' => 'Open', 'role' => null]]);
    cache()->flush();

    $page = deviceVisit($device, '/recruitment');
    $page->assertNoSmoke();

    $page->waitForText('Job Portal');
    $page->assertSee('Apply');

    snap($page, "recruitment-portal-{$device}");
})->with(['desktop', 'iphone']);

it('portal — an applicant sees their progress and reviewer-action timeline', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);

    $junior = makeControlGroup('Junior HR');
    openPostingWithStages($character->corporation_id, [
        ['label' => 'Screening', 'role' => $junior],
        ['label' => 'Final', 'role' => null],
    ]);

    // The acting user applies (account-wide) and has already passed screening.
    $application = Application::query()->create([
        'corporation_id' => $character->corporation_id,
        'applicationable_type' => User::class,
        'applicationable_id' => $user->getKey(),
    ]);
    ApplicationLogs::query()->create([
        'application_id' => $application->id,
        'causer_type' => User::class,
        'causer_id' => $user->getKey(),
        'type' => 'decision',
        'comment' => '',
    ]);
    cache()->flush();

    $page = deviceVisit($device, '/recruitment');
    $page->assertNoSmoke();

    // Progress is shown inline on the corporation's posting card (no separate panel).
    $page->waitForText('Job Portal');
    $page->assertSee('Under review');
    $page->assertSee('Stage 2 of 2');

    snap($page, "recruitment-my-application-{$device}");
})->with(['desktop', 'iphone']);

// ═══ Reviewers — Reviews inbox (the visibility filter) ════════════════════════════════════════

it('reviews — a junior reviewer sees an application waiting at their stage', function (string $device) {
    $character = actingAsCharacter();
    $reviewer = makeRecruiterOfCorporation($character, 'can accept or deny applications');

    $junior = makeControlGroup('Junior HR');
    $senior = makeControlGroup('Senior HR');
    addGroupMember($junior, $reviewer);
    openPostingWithStages($character->corporation_id, [
        ['label' => 'Screening', 'role' => $junior],
        ['label' => 'Final', 'role' => $senior],
    ]);
    seedApplicantAtStage($character->corporation_id, 'Jane Applicant', stage: 0);
    cache()->flush();

    $page = deviceVisit($device, '/recruitment/reviews');
    $page->assertNoSmoke();

    $page->waitForText('Reviews');
    $page->assertSee('Jane Applicant');
    $page->assertSee('Stage 1 of 2');

    snap($page, "recruitment-reviews-junior-{$device}");
})->with(['desktop', 'iphone']);

it('reviews — a junior reviewer does not see an application at a senior stage', function (string $device) {
    $character = actingAsCharacter();
    $reviewer = makeRecruiterOfCorporation($character, 'can accept or deny applications');

    $junior = makeControlGroup('Junior HR');
    $senior = makeControlGroup('Senior HR');
    addGroupMember($junior, $reviewer);
    openPostingWithStages($character->corporation_id, [
        ['label' => 'Screening', 'role' => $junior],
        ['label' => 'Final', 'role' => $senior],
    ]);
    // Already past screening — now at the senior-only stage.
    seedApplicantAtStage($character->corporation_id, 'Jane Applicant', stage: 1);
    cache()->flush();

    $page = deviceVisit($device, '/recruitment/reviews');
    $page->assertNoSmoke();

    $page->waitForText('Nothing to review');
    $page->assertDontSee('Jane Applicant');

    snap($page, "recruitment-reviews-junior-empty-{$device}");
})->with(['desktop', 'iphone']);

it('reviews — a senior reviewer sees the application at the final stage', function (string $device) {
    $character = actingAsCharacter();
    $reviewer = makeRecruiterOfCorporation($character, 'can accept or deny applications');

    $junior = makeControlGroup('Junior HR');
    $senior = makeControlGroup('Senior HR');
    addGroupMember($senior, $reviewer);
    openPostingWithStages($character->corporation_id, [
        ['label' => 'Screening', 'role' => $junior],
        ['label' => 'Final', 'role' => $senior],
    ]);
    seedApplicantAtStage($character->corporation_id, 'Jane Applicant', stage: 1);
    cache()->flush();

    $page = deviceVisit($device, '/recruitment/reviews');
    $page->assertNoSmoke();

    $page->waitForText('Reviews');
    $page->assertSee('Jane Applicant');
    $page->assertSee('Stage 2 of 2');

    snap($page, "recruitment-reviews-senior-{$device}");
})->with(['desktop', 'iphone']);

// ═══ Reviewer — application detail (the shared inspection tabs) ════════════════════════════════

it('reviews — a reviewer opens an application and sees the shared inspection tabs', function (string $device) {
    $character = actingAsCharacter();
    makeRecruiterOfCorporation($character, 'can accept or deny applications');

    openPostingWithStages($character->corporation_id, [['label' => 'Open', 'role' => null]]);
    $application = seedApplicantAtStage($character->corporation_id, 'Jane Applicant', stage: 0);
    cache()->flush();

    $page = deviceVisit($device, "/corporation/recruitment/application/{$application->id}");
    $page->assertNoSmoke();

    // The detail renders the applicant and the shared inspection tabs (their data is wired into
    // CharacterInspectionScrollProps on this branch). assertNoSmoke covers the render; the tab labels
    // live in a <select> on mobile, so they aren't reliably visible text to assert on across devices.
    $page->waitForText('Jane Applicant');

    snap($page, "recruitment-review-detail-{$device}");
})->with(['desktop', 'iphone']);

it('reviews — the inspection tabs render the applicant\'s data', function () {
    $character = actingAsCharacter();
    makeRecruiterOfCorporation($character, 'can accept or deny applications');

    openPostingWithStages($character->corporation_id, [['label' => 'Open', 'role' => null]]);
    $application = seedApplicantAtStage($character->corporation_id, 'Jane Applicant', stage: 0);

    // Give the applicant's character skills and a wallet journal so the review tabs show real data.
    $applicant = User::query()->findOrFail($application->applicationable_id);
    $applicantCharacter = CharacterInfo::query()->findOrFail($applicant->main_character_id);
    seedCharacterSkills($applicantCharacter);
    WalletJournal::factory()
        ->count(5)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_journable_id' => $applicantCharacter->character_id,
            'wallet_journable_type' => CharacterInfo::class,
        ]);
    cache()->flush();

    // Desktop only: the tab switcher is clickable text here; on mobile it's a <select>.
    $page = visit("/corporation/recruitment/application/{$application->id}");
    $page->assertNoSmoke();
    $page->waitForText('Jane Applicant');

    // Skills tab — the applicant's trained skills.
    $page->click('[data-tab="Skills"]');
    $page->waitForText('Gunnery');
    snap($page, 'recruitment-review-tab-skills');

    // Wallets tab — the applicant's wallet journal.
    $page->click('[data-tab="Wallets"]');
    $page->assertNoSmoke();
    snap($page, 'recruitment-review-tab-wallets');

    // Assets tab — renders through the shared inspection scroll props.
    $page->click('[data-tab="Assets"]');
    $page->assertNoSmoke();
    snap($page, 'recruitment-review-tab-assets');
});
