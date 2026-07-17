<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

/*
 * Recruitment landing (/corporation/recruitment) browser test. The affiliated-corporation
 * picker (CorporationList.vue) is now an Inertia <InfiniteScroll> over the `corporations`
 * scroll prop instead of the axios/Ziggy useInfinityScrolling loader, and the cards are
 * rebuilt on the shared CardWithHeader + Button. A superuser passes the recruitment gate
 * and marks every not-yet-enlisted corporation as enlistable, so the cards render without
 * wiring up affiliations. Provisioning via actingAsCharacter() (core tests/Pest.php).
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

it('renders the affiliated corporation list for a recruitment manager', function (string $device) {
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

    // Page shell + the picker section the redesigned CorporationList lives under.
    $page->waitForText('Corporation Recruitment');
    $page->waitForText('Open a corporation for recruitment');

    // A corporation card with its two footer actions (Button + heroicons).
    $page->waitForText($corporation->name);
    $page->assertSee('Recruits only');
    $page->assertSee('All characters');

    // The infinite-scroll row list is present with at least the seeded corporations.
    $page->assertScript("document.querySelectorAll('#recruitment-corporation-list > li').length >= 2");

    snap($page, "recruitment-corporation-list-{$device}");
})->with(['desktop', 'iphone']);
