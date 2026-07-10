<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Models\Onboarding;

/*
 * Character wallet infinite-scroll browser test — runs against the assembled core app.
 *
 * Verifies the Inertia v3 <InfiniteScroll> migration for BOTH lists on the wallet
 * view: journal_<id> and transaction_<id> scroll props. Each list renders a bounded
 * first page and, when its container is scrolled to the bottom, merges the next page
 * in — with no JS/console errors.
 *
 * The character is seeded with 40 journal + 40 transaction rows spaced 6h apart
 * (adjacent entries never more than a day apart), spanning multiple paginator pages.
 * No permission granted: a user always sees their OWN character's wallet.
 */

uses(RefreshDatabase::class);

it('merges the next journal and transaction pages in on scroll', function () {
    Queue::fake();

    $character = CharacterInfo::factory()->create();

    $user = new User;
    $user->main_character_id = $character->character_id;
    $user->save();

    CharacterUser::create([
        'user_id' => $user->getKey(),
        'character_id' => $character->character_id,
        'character_owner_hash' => sha1((string) $character->character_id),
    ]);

    Onboarding::create(['user_id' => $user->getKey()]);

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

    $this->actingAs($user);

    // Scrolling a list's own container to the bottom must merge the next page in.
    $assertScrollMerges = function ($page, string $bodyId) {
        $rows = "#{$bodyId} > li";
        // First page only (seeded 40 > the 15/page default, so not everything loads).
        $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
        expect($before)->toBeGreaterThan(0);

        $page->script("document.getElementById('{$bodyId}').closest('.overflow-y-auto').scrollTo(0, 1e6)");
        $page->wait(1);

        // Scrolling merged at least one more page in.
        $after = (int) $page->script("document.querySelectorAll('{$rows}').length");
        expect($after)->toBeGreaterThan($before);
    };

    $page = visit('/character/wallets');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertSee('Transaction');
    $page->waitForText('Journal');

    $assertScrollMerges($page, "journal-body-{$character->character_id}");
    $assertScrollMerges($page, "transaction-body-{$character->character_id}");

    $page->screenshot(true, 'wallet-infinite-scroll');
});
