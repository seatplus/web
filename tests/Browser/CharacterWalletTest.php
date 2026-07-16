<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;

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
 * Character wallet browser tests — run against the real assembled core app.
 *
 * Covers the modernized wallet view: journal + transaction infinite scroll (Inertia
 * <InfiniteScroll> over scroll props) and the prop-fed ref_type filter. No permission
 * is granted — a user always sees their OWN character's wallet. Provisioning comes from
 * the suite helper actingAsCharacter() (tests/Browser/Pest.php).
 */

uses(RefreshDatabase::class);

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

if (! function_exists('attachOwnedCharacter')) {
    /**
     * Attach an additional owned character to the same user that owns $existing, so a
     * browser test can exercise the multi-character case — character-scoped pages
     * aggregate over / render per every character the logged-in user owns, not just
     * the main. Returns the newly attached character. Guarded so each Browser test
     * file can define it standalone without colliding when the suite loads several.
     */
    function attachOwnedCharacter(CharacterInfo $existing): CharacterInfo
    {
        $user = CharacterUser::query()
            ->where('character_id', $existing->character_id)
            ->firstOrFail()
            ->user;

        $character = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        return $character;
    }
}

it('merges the next journal and transaction pages in on scroll', function (string $device) {
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

        // Re-scroll to the bottom on every poll (comma expression) so the observer
        // reliably fires even if layout hadn't settled on the first attempt; passes
        // once the next page has merged in.
        $page->assertScript("(document.getElementById('{$bodyId}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");
    };

    $page = deviceVisit($device, '/character/wallets');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertSee('Transaction');
    $page->waitForText('Journal');

    $assertScrollMerges($page, "journal-body-{$character->character_id}");
    $assertScrollMerges($page, "transaction-body-{$character->character_id}");

    snap($page, "character-wallet-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);

it('filters the wallet journal by ref_type (and resets, not merges)', function (string $device) {
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

    $page = deviceVisit($device, '/character/wallets');
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
    snap($page, "character-wallet-filter-{$device}");
})->with(['desktop', 'iphone']);

it('renders a wallet card for every character the user owns', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    // A handful of journal rows per character so each card's list renders. The page
    // renders one wallet (its own journal_<id> scroll prop / list) per owned character,
    // so both journal bodies must be present — a single-character render would only
    // emit the main character's.
    collect([$mainCharacter, $secondCharacter])
        ->each(fn (CharacterInfo $character) => WalletJournal::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
            ->create([
                'wallet_journable_id' => $character->character_id,
                'wallet_journable_type' => CharacterInfo::class,
            ]));

    $page = deviceVisit($device, '/character/wallets');
    $page->assertNoSmoke();
    $page->waitForText('Journal');

    $page->assertPresent("#journal-body-{$mainCharacter->character_id}");
    $page->assertPresent("#journal-body-{$secondCharacter->character_id}");

    snap($page, "character-wallet-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);
