<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Mail\MailRecipients;

/*
 * Character mail browser test — the mail header list (desktop aside + mobile) is now
 * an Inertia <InfiniteScroll> over the `mailHeaders` scroll prop instead of the axios
 * InfiniteLoadingHelper. Seeds 40 mails addressed to the logged-in character (6h apart)
 * and asserts scrolling the list merges the next page in. No permission needed —
 * own-character access. Provisioning helper: tests/Browser/Pest.php.
 */

uses(RefreshDatabase::class);

it('merges the next page of mail headers on scroll', function () {
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

    $rows = '#desktop-mail-list > li';

    $page = visit('/character/mails');
    $page->assertNoSmoke();
    $page->waitForText('Character Mails');

    // First page only (40 seeded > the 15/page default).
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll the list's container on each poll until the next page merges in.
    $page->assertScript("(document.getElementById('desktop-mail-list').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    $page->screenshot(true, 'character-mails-infinite-scroll');
});
