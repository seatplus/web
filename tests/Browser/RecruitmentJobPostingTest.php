<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;

/*
 * Recruitment "Job Posting" management browser test — the HR flow where a manager posts a corp for
 * recruitment and configures it. Consolidates the former RecruitmentListTest (#1588) +
 * RecruitmentConfigTest (#1583) into one suite and asserts the new user-facing "Job Posting" copy.
 *
 * The surfaces exercised here, all against the real assembled core app:
 *
 *  1. Recruitment index (/corporation/recruitment): now lists ONLY corporations that already have a
 *     Job Posting (one CorporationRecruitment card each) plus an empty state when there are none.
 *     The always-visible openable-corporation list (CorporationList.vue) is gone. Creating a posting
 *     is an inline panel revealed by a "Create job posting" button: a corporation search (the same
 *     ESI "applies to" picker the ACL affiliations use — EsiAutosuggest, shows nothing until you
 *     type) plus a labelled "who can apply" choice. The ESI search itself is token-gated and cannot
 *     be driven offline (same constraint as the ACL browser tests), so the create POST path is
 *     covered by the RecruitmentLifeCycleTest feature test; here we assert the panel + inputs render.
 *
 *  2. Configuration (/corporation/recruitment/{corporation_id}): corporation-scoped, rendered by
 *     EnlistmentsController@edit behind CheckAuthorization:'can open or close corporations for
 *     recruitment,director'. Access is granted via the in-game Director corp role
 *     (giveCorporationRole) — not superuser — mirroring a real senior-HR director. An Enlistment
 *     record must exist for @edit to hydrate the (edit-mode) config card.
 *
 * Provisioning helpers (actingAsCharacter, giveCorporationRole) live in the core app's
 * tests/Pest.php — browser tests run against the assembled core, whose Pest.php is not overlaid
 * here, so the viewport/screenshot helpers below are defined guarded alongside the suite's tests.
 */

uses(RefreshDatabase::class);

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        if ($device === 'iphone') {
            return new PendingAwaitablePage(
                Playwright::defaultBrowserType(),
                Device::IPHONE_15,
                $url,
                $options,
            );
        }

        return visit($url, $options);
    }
}

if (! function_exists('snap')) {
    /**
     * Settle before screenshotting: flip lazy EVE-image portraits/logos to eager so off-screen
     * (full-page) images fetch, wait for the network to go idle, then capture — so screenshots show
     * resolved images instead of loading placeholders. Best-effort: a slow/absent image won't fail.
     */
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

it('shows an empty state and reveals an inline create panel', function (string $device) {
    $character = actingAsCharacter();

    // Superuser passes the recruitment gate (CanUserService bypass) and may manage recruitment,
    // so the "Create job posting" entry point renders.
    userOfCharacter($character->character_id)
        ->givePermissionTo(Permission::findOrCreate('superuser'));

    $page = deviceVisit($device, '/corporation/recruitment');
    $page->assertNoSmoke();

    // Page shell.
    $page->waitForText('Corporation Recruitment');

    // No postings yet → the empty state and the create entry point are shown.
    $page->waitForText('No job postings yet');
    $page->assertSee('Create job posting');

    // The old always-visible openable-corporation list is gone.
    $page->assertScript("!document.querySelector('#recruitment-corporation-list')");

    // Create is an inline panel (no modal, no navigation) revealed by the button.
    $page->click('Create job posting');
    $page->waitForText('Create a job posting');
    $page->assertSee('Who can apply?');
    $page->assertSee('Recruits only');
    $page->assertSee('All characters');

    // The corporation search is the ESI "applies to" picker: an input that shows nothing until you
    // type. Its input carries id="Corporation" (from the field label); no option list before typing.
    $page->assertScript("!!document.querySelector('#Corporation')");
    $page->assertScript("document.querySelectorAll('[role=option]').length === 0");
    $page->assertNoSmoke();

    snap($page, "job-posting-empty-create-{$device}");
})->with(['desktop', 'iphone']);

it('lists only corporations that have a job posting', function (string $device) {
    $character = actingAsCharacter();

    userOfCharacter($character->character_id)
        ->givePermissionTo(Permission::findOrCreate('superuser'));

    // Real NPC corporation id so images.evetech.net serves a real logo in the screenshot.
    $corporation = CorporationInfo::factory()->create([
        'corporation_id' => 1000107,
        'name' => 'Science and Trade Institute',
    ]);

    // A posted corporation → exactly one card, no empty state.
    Enlistment::create([
        'corporation_id' => $corporation->corporation_id,
        'type' => 'user',
        'steps' => '',
    ]);

    $page = deviceVisit($device, '/corporation/recruitment');
    $page->assertNoSmoke();

    $page->waitForText('Corporation Recruitment');

    // The posted corporation renders as a card; the empty state is gone.
    $page->waitForText($corporation->name);
    $page->assertDontSee('No job postings yet');

    // Still no always-visible openable-corporation list — postings are the only cards.
    $page->assertScript("!document.querySelector('#recruitment-corporation-list')");
    $page->assertNoSmoke();

    snap($page, "job-posting-list-{$device}");
})->with(['desktop', 'iphone']);

it('a corp director configures a job posting', function (string $device) {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role → satisfies the ,director branch of the recruitment gate (no superuser).
    giveCorporationRole($character);

    // The open enlistment @edit hydrates the page from.
    Enlistment::create([
        'corporation_id' => $corporationId,
        'type' => 'user',
        'steps' => 'First interview; Second interview',
    ]);

    $page = deviceVisit($device, "/corporation/recruitment/{$corporationId}");
    $page->assertNoSmoke();

    // Page header + the config card, both now labelled with the "Job Posting" copy
    // (edit mode shows the distinctive "Review Process Steps" field).
    $page->waitForText('Job Posting Settings');
    $page->assertSee('Job Posting');
    $page->assertSee('Review Process Steps');

    // Region/System filter card and the items watchlist card both render.
    $page->assertSee('Region or System Filter');
    $page->assertSee('Items Watchlist');

    // A config form is present: the review-process-steps input and the save actions render.
    $page->assertScript("!!document.querySelector('#steps')");
    $page->assertSee('Save');

    snap($page, "job-posting-config-{$device}");
})->with(['desktop', 'iphone']);
