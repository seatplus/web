<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        // iPhone: build a persistent page at the mobile viewport the same way visit() builds the
        // desktop one, so the page loads mobile from the start (no desktop-load-then-resize reflow,
        // no per-call re-navigation like ->on()->iPhone15()).
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

/*
 * Corporation member tracking browser test.
 *
 * The page renders one card per affiliated corporation, each backed by a
 * members_<corporation> infinite-scroll prop (Inertia <InfiniteScroll> over a page
 * scroll prop) — the migration off the legacy axios/Ziggy useInfinityScrolling loader.
 * Access is granted by the in-game Director corp role (giveCorporationRole) — not
 * superuser — which CanUserService accepts, mirroring a real director viewing their
 * corp. Provisioning helpers live in core's browser context (actingAsCharacter /
 * giveCorporationRole).
 */

uses(RefreshDatabase::class);

it('merges the next member page in on scroll', function (string $device) {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role → grants member-tracking access (no superuser / Spatie permission).
    giveCorporationRole($character);

    // 40 tracked members (default page size 15 → several pages) so scrolling the card's
    // own container triggers InfiniteScroll to merge the next page. Distinct characters
    // satisfy the (corporation_id, character_id) unique key.
    CorporationMemberTracking::factory()
        ->count(40)
        ->sequence(fn ($sequence) => [
            'start_date' => now()->subDays($sequence->index),
            'logon_date' => now()->subHours($sequence->index),
        ])
        ->create(['corporation_id' => $corporationId]);

    $page = deviceVisit($device, '/corporation/tracking');
    $page->assertNoSmoke();
    $page->assertSee('Member Tracking');
    $page->waitForText('Member Tracking');

    // Scroll the card's own container to the bottom → InfiniteScroll's end trigger fires
    // and merges the next page. assertScript auto-polls until the row count grows.
    $bodyId = "members-body-{$corporationId}";
    $rows = "#{$bodyId} > li";
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll to the bottom on every poll (comma expression) so the observer reliably
    // fires even if layout hadn't settled on the first attempt; passes once the next page
    // has merged in.
    $page->assertScript("(document.getElementById('{$bodyId}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    snap($page, "corporation-member-tracking-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);
