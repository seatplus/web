<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Corporation\CorporationDivision;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;

/*
 * Corporation wallet browser test.
 *
 * The corporation wallet is division-scoped: both lists render
 * journal_<corporation>_<division> / transaction_<corporation>_<division> scroll props.
 * Access is granted by the in-game Director corp role (giveCorporationRole) — not
 * superuser — which CanUserService accepts, mirroring a real director viewing their
 * corp wallet. CorporationDivision is created directly (its factory has a `division_typ`
 * typo vs. the real `division_type` column). Provisioning helpers live in
 * tests/Browser/Pest.php.
 */

uses(RefreshDatabase::class);

it('merges the next corporation journal and transaction pages in on scroll', function () {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role → grants corp wallet access (no superuser / Spatie permission).
    giveCorporationRole($character);

    // The wallet division the page renders a card for.
    CorporationDivision::create([
        'corporation_id' => $corporationId,
        'division_type' => 'wallet',
        'division_id' => 1,
        'name' => 'Master Wallet',
    ]);

    WalletJournal::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_journable_id' => $corporationId,
            'wallet_journable_type' => CorporationInfo::class,
            'division' => 1,
        ]);

    WalletTransaction::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_transactionable_id' => $corporationId,
            'wallet_transactionable_type' => CorporationInfo::class,
            'division' => 1,
        ]);

    // Scroll a list's own container to the bottom → InfiniteScroll's end trigger fires
    // and merges the next page. assertScript auto-polls until the row count grows.
    $assertScrollMerges = function ($page, string $bodyId) {
        $rows = "#{$bodyId} > li";
        $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
        expect($before)->toBeGreaterThan(0);

        // Re-scroll to the bottom on every poll (comma expression) so the observer
        // reliably fires even if layout hadn't settled on the first attempt; passes
        // once the next page has merged in.
        $page->assertScript("(document.getElementById('{$bodyId}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");
    };

    $page = visit('/corporation/wallet');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertSee('Transaction');
    $page->waitForText('Journal');

    $assertScrollMerges($page, "journal-body-{$corporationId}-1");
    $assertScrollMerges($page, "transaction-body-{$corporationId}-1");

    $page->screenshot(true, 'corporation-wallet-infinite-scroll');
});
