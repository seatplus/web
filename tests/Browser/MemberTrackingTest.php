<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;

require_once __DIR__.'/helpers.php';

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
