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
 * Recruitment "Job Posting" management browser test — the HR flow where a manager browses
 * corporations, opens one for recruitment and configures it. Consolidates the former
 * RecruitmentListTest (#1588) + RecruitmentConfigTest (#1583) into one suite and asserts the
 * new user-facing "Job Posting" copy.
 *
 * The two surfaces exercised here, both against the real assembled core app:
 *
 *  1. Recruitment index (/corporation/recruitment): the affiliated-corporation picker
 *     (CorporationList.vue) is now an Inertia <InfiniteScroll> over the `corporations` scroll
 *     prop (no more axios/Ziggy useInfinityScrolling), cards rebuilt on the shared
 *     CardWithHeader + Button. A superuser passes the recruitment gate and marks every
 *     not-yet-enlisted corporation enlistable, so the cards render without wiring affiliations.
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

it('renders the affiliated corporation list to create a job posting', function (string $device) {
    $character = actingAsCharacter();

    // Superuser: passes the CheckAuthorization recruitment gate (CanUserService bypass) and
    // makes every not-yet-enlisted corporation enlistable, so the picker cards render.
    userOfCharacter($character->character_id)
        ->givePermissionTo(Permission::findOrCreate('superuser'));

    // Real NPC corporation ids so images.evetech.net serves real logos in the screenshot.
    $corporation = CorporationInfo::factory()->create([
        'corporation_id' => 1000107,
        'name' => 'Science and Trade Institute',
    ]);

    CorporationInfo::factory()->create([
        'corporation_id' => 1000035,
        'name' => 'Caldari Provisions',
    ]);

    $page = deviceVisit($device, '/corporation/recruitment');
    $page->assertNoSmoke();

    // Page shell + the "Job Posting" copy the redesigned picker section lives under.
    $page->waitForText('Corporation Recruitment');
    $page->waitForText('Create a job posting');

    // A corporation card with its two footer actions (Button + heroicons).
    $page->waitForText($corporation->name);
    $page->assertSee('Recruits only');
    $page->assertSee('All characters');

    // The infinite-scroll row list is present with at least the seeded corporations.
    $page->assertScript("document.querySelectorAll('#recruitment-corporation-list > li').length >= 2");

    snap($page, "job-posting-corporation-list-{$device}");
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
