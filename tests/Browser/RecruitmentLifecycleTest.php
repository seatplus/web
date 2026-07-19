<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

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

if (! function_exists('deviceVisit')) {
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        if ($device === 'iphone') {
            return new PendingAwaitablePage(Playwright::defaultBrowserType(), Device::IPHONE_15, $url, $options);
        }

        return visit($url, $options);
    }
}

if (! function_exists('snap')) {
    function snap($page, string $name): void
    {
        $page->script("document.querySelectorAll('img').forEach((i) => { i.loading = 'eager'; });");
        $page->waitForEvent('networkidle');
        $page->screenshot(true, $name);
    }
}

if (! function_exists('userOfCharacter')) {
    function userOfCharacter(int $characterId): User
    {
        return CharacterUser::query()->where('character_id', $characterId)->firstOrFail()->user;
    }
}

if (! function_exists('makeControlGroup')) {
    function makeControlGroup(string $name): Role
    {
        return Role::findById(Role::create(['name' => $name])->id);
    }
}

if (! function_exists('addGroupMember')) {
    function addGroupMember(Role $role, User $user): void
    {
        RoleMembership::create([
            'role_id' => $role->id,
            'entity_type' => User::class,
            'entity_id' => $user->getKey(),
            'status' => 'active',
        ]);
        $user->assignRole($role);
    }
}

if (! function_exists('makeRecruiterOfCorporation')) {
    /** Grant the acting user recruiter access to their corporation (permission + director role). */
    function makeRecruiterOfCorporation(CharacterInfo $character, string $permission): User
    {
        $user = userOfCharacter($character->character_id);
        $user->givePermissionTo(Permission::findOrCreate($permission));

        CharacterRole::updateOrCreate(
            ['character_id' => $character->character_id],
            ['roles' => ['Director']],
        );

        return $user;
    }
}

if (! function_exists('openPostingWithStages')) {
    /**
     * @param  array<int, array{label: string, role: ?Role}>  $stages
     */
    function openPostingWithStages(int $corporationId, array $stages): void
    {
        Enlistment::query()->updateOrCreate(['corporation_id' => $corporationId], ['type' => 'user']);

        foreach ($stages as $position => $stage) {
            EnlistmentReviewRound::query()->updateOrCreate(
                ['corporation_id' => $corporationId, 'position' => $position],
                ['label' => $stage['label'], 'role_id' => $stage['role']?->id],
            );
        }
    }
}

if (! function_exists('seedApplicantAtStage')) {
    /** Create an applicant with an open application to $corporationId, already advanced to $stage. */
    function seedApplicantAtStage(int $corporationId, string $name, int $stage = 0): Application
    {
        $character = CharacterInfo::factory()->create(['name' => $name]);

        $user = new User;
        $user->main_character_id = $character->character_id;
        $user->save();

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        $application = Application::query()->create([
            'corporation_id' => $corporationId,
            'applicationable_type' => User::class,
            'applicationable_id' => $user->getKey(),
        ]);

        // Each recorded decision advances the application one stage.
        for ($i = 0; $i < $stage; $i++) {
            ApplicationLogs::query()->create([
                'application_id' => $application->id,
                'causer_type' => User::class,
                'causer_id' => $user->getKey(),
                'type' => 'decision',
                'comment' => '',
            ]);
        }

        return $application;
    }
}

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
