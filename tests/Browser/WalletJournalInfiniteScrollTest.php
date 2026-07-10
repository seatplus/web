<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Models\Onboarding;

/*
 * Wallet journal infinite-scroll browser test — runs against the assembled core app.
 *
 * Verifies the Inertia v3 <InfiniteScroll> migration (WalletsController::index emits
 * a `journal_<id>` scroll prop; WalletJournalComponent consumes it): the first page
 * renders a bounded number of rows, and scrolling the journal container to the
 * bottom merges the next page in (more rows appear) — with no JS/console errors.
 *
 * The character is seeded with 40 journal rows spaced 6h apart (adjacent entries
 * never more than a day apart), spanning multiple paginator pages. Factory-name
 * guessing for the seatplus packages is configured in core's tests/TestCase::setUp.
 */

uses(RefreshDatabase::class);

it('merges the next wallet journal page in on scroll', function () {
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

    // Superuser so getCharacterIds() returns this character irrespective of affiliations.
    $user->givePermissionTo(Permission::findOrCreate('superuser'));

    // 40 entries, one every 6 hours descending → adjacent entries are never more
    // than a day apart, and the list spans several paginator pages (default 15/page).
    WalletJournal::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['date' => now()->subHours($sequence->index * 6)])
        ->create([
            'wallet_journable_id' => $character->character_id,
            'wallet_journable_type' => CharacterInfo::class,
        ]);

    $this->actingAs($user);

    $listSelector = "#journal-body-{$character->character_id}";

    $page = visit('/character/wallets');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->waitForText('Journal');

    // First load: only the first paginator page is present (not all 40 rows).
    $before = (int) $page->script("document.querySelectorAll('{$listSelector} > li').length");
    expect($before)->toBeGreaterThan(0)->toBeLessThan(40);

    // Scroll the journal's own scroll container to the bottom → InfiniteScroll's
    // end trigger intersects and the next page is fetched + merged.
    $page->script("document.getElementById('journal-body-{$character->character_id}').closest('.overflow-y-auto').scrollTo(0, 1e6)");
    $page->wait(1);

    $after = (int) $page->script("document.querySelectorAll('{$listSelector} > li').length");
    expect($after)->toBeGreaterThan($before)->toBeLessThanOrEqual(40);

    $page->screenshot(true, 'wallet-journal-infinite-scroll');
});
