<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Corporation\CorporationDivision;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Models\Onboarding;

/*
 * Corporation wallet infinite-scroll browser test.
 *
 * The corporation wallet is division-scoped: both lists render
 * journal_<corporation>_<division> / transaction_<corporation>_<division> scroll
 * props. Access is granted by the in-game Director corp role (CharacterRole
 * roles => ['Director']) — not superuser — which CanUserService accepts, so this
 * mirrors a real director viewing their corp wallet.
 *
 * CorporationDivision is created directly (its factory has a `division_typ` typo vs.
 * the real `division_type` column). Factory-name guessing is set in core's TestCase.
 */

uses(RefreshDatabase::class);

it('merges the next corporation journal and transaction pages in on scroll', function () {
    Queue::fake();

    $character = CharacterInfo::factory()->create();
    $corporationId = $character->corporation->corporation_id;

    $user = new User;
    $user->main_character_id = $character->character_id;
    $user->save();

    CharacterUser::create([
        'user_id' => $user->getKey(),
        'character_id' => $character->character_id,
        'character_owner_hash' => sha1((string) $character->character_id),
    ]);

    Onboarding::create(['user_id' => $user->getKey()]);

    // Director corp role → grants corp wallet access (no superuser / Spatie permission).
    // CharacterInfo::factory() already creates an (empty) CharacterRole, so update it.
    CharacterRole::updateOrCreate(
        ['character_id' => $character->character_id],
        ['roles' => ['Director']],
    );

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

    $this->actingAs($user);

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

    $page = visit('/corporation/wallet');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertSee('Transaction');
    $page->waitForText('Journal');

    $assertScrollMerges($page, "journal-body-{$corporationId}-1");
    $assertScrollMerges($page, "transaction-body-{$corporationId}-1");

    $page->screenshot(true, 'corporation-wallet-infinite-scroll');
});
