<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Models\Onboarding;

/*
 * Wallet journal ref_type filter browser test.
 *
 * Seeds two distinct ref_types (30 player_donation + 3 bounty) and drives the real
 * WalletFilter UI: selecting "bounty" must reload the journal scroll prop with the
 * filter AND reset it, so the list shows only the 3 bounty rows — not the filtered
 * page merged onto the previously-loaded rows (the reset regression).
 *
 * Superuser so the journalTypes autosuggest endpoint (CheckAuthorizationWithExtendedScope)
 * is reachable. Cache::flush() clears the cached ref_type list + per-user permission cache.
 */

uses(RefreshDatabase::class);

it('filters the wallet journal by ref_type (and resets, not merges)', function () {
    Queue::fake();
    Cache::flush();

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
    $user->givePermissionTo(Permission::findOrCreate('superuser'));

    // 30 player_donation (newest) + 3 bounty (oldest), 6h apart. So the unfiltered
    // first page is all player_donation; filtering to bounty must surface exactly 3.
    WalletJournal::factory()
        ->count(30)
        ->sequence(fn ($s) => ['date' => now()->subHours($s->index * 6)])
        ->create([
            'wallet_journable_id' => $character->character_id,
            'wallet_journable_type' => CharacterInfo::class,
            'ref_type' => 'player_donation',
        ]);

    WalletJournal::factory()
        ->count(3)
        ->sequence(fn ($s) => ['date' => now()->subHours((30 + $s->index) * 6)])
        ->create([
            'wallet_journable_id' => $character->character_id,
            'wallet_journable_type' => CharacterInfo::class,
            'ref_type' => 'bounty',
        ]);

    $this->actingAs($user);

    $rows = "#journal-body-{$character->character_id} > li";

    $page = visit('/character/wallets');
    $page->assertNoSmoke();
    $page->assertSee('Journal');
    $page->assertCount($rows, 15); // first page of 33, all player_donation

    // Drive the real filter: type into the WalletFilter autosuggest and pick "bounty".
    $page->type('Wallet Journal entries', 'bounty');
    $page->waitForText('bounty');
    $page->click('bounty');

    // Filtered + reset: only the 3 bounty rows remain (not 15 + merged).
    $page->assertCount($rows, 3);
    $page->screenshot(true, 'wallet-journal-filter');
});
