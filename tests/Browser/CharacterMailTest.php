<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Mail\MailRecipients;

require_once __DIR__.'/helpers.php';

/*
 * Character mail browser test — the mail header list (desktop aside + mobile) is now
 * an Inertia <InfiniteScroll> over the `mailHeaders` scroll prop instead of the axios
 * InfiniteLoadingHelper. Seeds 40 mails addressed to the logged-in character (6h apart)
 * and asserts scrolling the list merges the next page in. No permission needed —
 * own-character access. Provisioning helper: tests/Browser/Pest.php.
 */

uses(RefreshDatabase::class);

it('merges the next page of mail headers on scroll', function (string $device) {
    $character = actingAsCharacter();

    Mail::factory()
        ->count(40)
        ->sequence(fn ($sequence) => ['timestamp' => now()->subHours($sequence->index * 6)->format('Y-m-d H:i:s')])
        ->create()
        ->each(fn (Mail $mail) => MailRecipients::factory()->create([
            'mail_id' => $mail->id,
            'receivable_id' => $character->character_id,
            'receivable_type' => CharacterInfo::class,
        ]));

    // The visible list differs by viewport: aside #desktop-mail-list on md+, #mobile-mail-list below.
    $listId = $device === 'iphone' ? 'mobile-mail-list' : 'desktop-mail-list';
    $rows = "#{$listId} > li";

    $page = deviceVisit($device, '/character/mails');
    $page->assertNoSmoke();
    $page->waitForText('Character Mails');

    // First page only (40 seeded > the 15/page default).
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll the list's container on each poll until the next page merges in.
    $page->assertScript("(document.getElementById('{$listId}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    snap($page, "character-mails-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);

it('aggregates mail headers across all of the user\'s characters', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    // 6 mails per character (12 total, under the 15/page default) so the entire set
    // lands on the first page: a main-character-only list would render 6, the full
    // owned set renders 12 — the count alone proves the aggregation.
    collect([$mainCharacter, $secondCharacter])
        ->each(fn (CharacterInfo $character, int $characterIndex) => Mail::factory()
            ->count(6)
            ->sequence(fn ($sequence) => ['timestamp' => now()->subHours(($characterIndex * 6 + $sequence->index) * 6)->format('Y-m-d H:i:s')])
            ->create()
            ->each(fn (Mail $mail) => MailRecipients::factory()->create([
                'mail_id' => $mail->id,
                'receivable_id' => $character->character_id,
                'receivable_type' => CharacterInfo::class,
            ])));

    $rows = '#desktop-mail-list > li';

    $page = deviceVisit($device, '/character/mails');
    $page->assertNoSmoke();
    $page->waitForText('Character Mails');

    // Both characters' mails on a single page (6 + 6 = 12 < 15/page).
    $page->assertCount($rows, 12);

    snap($page, "character-mails-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);
