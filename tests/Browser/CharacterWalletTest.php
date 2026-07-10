<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;

/*
 * Character wallet browser tests — run against the real assembled core app.
 *
 * Covers the modernized wallet view: journal + transaction infinite scroll (Inertia
 * <InfiniteScroll> over scroll props) and the prop-fed ref_type filter. No permission
 * is granted — a user always sees their OWN character's wallet. Provisioning comes from
 * the suite helper actingAsCharacter() (tests/Browser/Pest.php).
 */

uses(RefreshDatabase::class);

it('merges the next journal and transaction pages in on scroll', function () {
    $character = actingAsCharacter();

    // 40 journal + 40 transaction rows, 6h apart (adjacent entries never more than a
    // day apart), spanning several paginator pages (default 15/page).
    WalletJournal::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_journable_id' => $character->character_id,
            'wallet_journable_type' => CharacterInfo::class,
        ]);

    WalletTransaction::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_transactionable_id' => $character->character_id,
            'wallet_transactionable_type' => CharacterInfo::class,
        ]);

    // Scroll a list's own container to the bottom → InfiniteScroll's end trigger fires
    // and merges the next page. assertScript auto-polls until the row count grows, so
    // there's no fixed-wait timing assumption.
    $assertScrollMerges = function ($page, string $bodyId) {
        $rows = "#{$bodyId} > li";
        $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
        expect($before)->toBeGreaterThan(0);

        $page->script("document.getElementById('{$bodyId}').closest('.overflow-y-auto').scrollTo(0, 1e6)");
        $page->assertScript("document.querySelectorAll('{$rows}').length > {$before}");
    };

    $page = visit('/character/wallets');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertSee('Transaction');
    $page->waitForText('Journal');

    $assertScrollMerges($page, "journal-body-{$character->character_id}");
    $assertScrollMerges($page, "transaction-body-{$character->character_id}");

    $page->screenshot(true, 'character-wallet-infinite-scroll');
});

it('filters the wallet journal by ref_type (and resets, not merges)', function () {
    $character = actingAsCharacter();

    // 30 player_donation (newest) + 3 bounty (oldest), 6h apart. So the unfiltered
    // first page is all player_donation; filtering to bounty must surface exactly 3.
    WalletJournal::factory()
        ->count(30)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_journable_id' => $character->character_id,
            'wallet_journable_type' => CharacterInfo::class,
            'ref_type' => 'player_donation',
        ]);

    WalletJournal::factory()
        ->count(3)
        ->sequence(fn ($sequence) => ['date' => now()->subHours((30 + $sequence->index) * 6)])
        ->create([
            'wallet_journable_id' => $character->character_id,
            'wallet_journable_type' => CharacterInfo::class,
            'ref_type' => 'bounty',
        ]);

    $rows = "#journal-body-{$character->character_id} > li";

    $page = visit('/character/wallets');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertCount($rows, 15); // first page of 33, all player_donation

    // Drive the real filter: type into the WalletFilter input and pick the "bounty" option.
    // The options come from the ref_types page prop (no autosuggest endpoint — no axios/Ziggy).
    $page->type('wallet-ref-type-filter', 'bounty');
    $page->waitForText('bounty');
    $page->click('bounty');

    // Filtered + reset: only the 3 bounty rows remain (not 15 + merged).
    $page->assertCount($rows, 3);
    $page->screenshot(true, 'character-wallet-filter');
});
