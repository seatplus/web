<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\SsoScopes;

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

if (! function_exists('realCharacterId')) {
    /**
     * A real EVE character id (verified CEO) so images.evetech.net serves a real portrait in
     * screenshots instead of the generic default it returns for fabricated ids. Picks one not yet
     * used in this (RefreshDatabase-isolated) test; falls back to a random id if the pool is spent.
     */
    function realCharacterId(): int
    {
        $pool = [197343093, 1319140135, 92081232, 1191750472, 94391213, 887625289, 1435633555, 1809892636];
        $available = array_values(array_diff($pool, CharacterInfo::query()->pluck('character_id')->all()));

        return $available[0] ?? fake()->unique()->numberBetween(9000000, 98000000);
    }
}

/*
 * Corporation member-tracking browser test.
 *
 * The page renders a card per affiliated corporation, each with its own
 * members_<corporation> scroll prop (Inertia <InfiniteScroll>). Access is granted by the
 * in-game Director corp role (giveCorporationRole) — not superuser / Spatie permission —
 * which CanUserService accepts, mirroring a real director viewing their corp's roster.
 *
 * getAffiliatedCorporations only surfaces a corporation that has SsoScopes (or its
 * alliance does) AND has member-tracking rows, so both are provisioned here.
 * Provisioning helpers (actingAsCharacter / giveCorporationRole) live in the core suite.
 */

uses(RefreshDatabase::class);

it('merges the next member tracking page in on scroll', function (string $device) {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role → grants member-tracking access (no superuser / Spatie permission).
    giveCorporationRole($character);

    // A corporation only appears on the page if it (or its alliance) has SsoScopes.
    SsoScopes::factory()->create([
        'morphable_id' => $corporationId,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
        'selected_scopes' => [],
    ]);

    // 40 tracked members (real EVE character ids while the pool lasts so the first page's
    // portraits resolve in the screenshot; the rest random), spanning several paginator
    // pages (default 15/page). start_date/logon_date make the rows render meaningful dates.
    collect(range(1, 40))->each(function (int $i) use ($corporationId) {
        $member = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        CorporationMemberTracking::factory()->create([
            'corporation_id' => $corporationId,
            'character_id' => $member->character_id,
            'start_date' => now()->subDays($i * 10),
            'logon_date' => now()->subDays($i),
        ]);
    });

    // Scroll the list's own container to the bottom → InfiniteScroll's end trigger fires and
    // merges the next page. assertScript auto-polls until the row count grows, so there's no
    // fixed-wait timing assumption. Each member renders two <li> (a desktop row + a mobile
    // card, one hidden per viewport), so the count grows in step either way.
    $bodyId = "member-tracking-body-{$corporationId}";
    $rows = "#{$bodyId} > li";

    $page = deviceVisit($device, '/corporation/tracking');
    $page->assertNoSmoke();
    $page->waitForText('Last Login');

    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll to the bottom on every poll (comma expression) so the observer reliably fires
    // even if layout hadn't settled on the first attempt; passes once the next page merged in.
    $page->assertScript("(document.getElementById('{$bodyId}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    snap($page, "corporation-member-tracking-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);
