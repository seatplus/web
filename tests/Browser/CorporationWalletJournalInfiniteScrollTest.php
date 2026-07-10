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
use Seatplus\Web\Models\Onboarding;

/*
 * Corporation wallet journal infinite-scroll browser test.
 *
 * The corporation wallet is division-scoped: WalletJournalComponent renders a
 * `journal_<corporation>_<division>` scroll prop. Access is granted by the in-game
 * Director corp role (CharacterRole roles => ['Director']) — not superuser — which
 * CanUserService::validateSimplePermissions accepts, so this mirrors a real director
 * viewing their corp wallet.
 *
 * CorporationDivision is created directly (its factory has a `division_typ` typo vs.
 * the real `division_type` column). Factory-name guessing is set in core's TestCase.
 */

uses(RefreshDatabase::class);

it('merges the next corporation wallet journal page in on scroll', function () {
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
    ]);

    // 40 journal rows for that corporation+division, 6h apart (adjacent entries never
    // more than a day apart), spanning several paginator pages.
    WalletJournal::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_journable_id' => $corporationId,
            'wallet_journable_type' => CorporationInfo::class,
            'division' => 1,
        ]);

    $this->actingAs($user);

    $rows = "#journal-body-{$corporationId}-1 > li";

    $page = visit('/corporation/wallet');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->waitForText('Journal');

    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0)->toBeLessThan(40);

    $page->script("document.getElementById('journal-body-{$corporationId}-1').closest('.overflow-y-auto').scrollTo(0, 1e6)");
    $page->wait(1);

    $after = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($after)->toBeGreaterThan($before)->toBeLessThanOrEqual(40);

    $page->screenshot(true, 'corporation-wallet-journal-infinite-scroll');
});
