<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Mail\MailRecipients;

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, array|string $url, array $options = []): mixed
    {
        $page = visit($url, $options);

        if ($device === 'iphone') {
            $page->resize(390, 844);
        }

        return $page;
    }
}

/*
 * Character mail browser test — the mail header list (desktop aside + mobile) is now
 * an Inertia <InfiniteScroll> over the `mailHeaders` scroll prop instead of the axios
 * InfiniteLoadingHelper. Seeds 40 mails addressed to the logged-in character (6h apart)
 * and asserts scrolling the list merges the next page in. No permission needed —
 * own-character access. Provisioning helper: tests/Browser/Pest.php.
 */

uses(RefreshDatabase::class);

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

        $character = CharacterInfo::factory()->create();

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        return $character;
    }
}

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

    $rows = '#desktop-mail-list > li';

    $page = deviceVisit($device, '/character/mails');
    $page->assertNoSmoke();
    $page->waitForText('Character Mails');

    // First page only (40 seeded > the 15/page default).
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll the list's container on each poll until the next page merges in.
    $page->assertScript("(document.getElementById('desktop-mail-list').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    $page->screenshot(true, "character-mails-infinite-scroll-{$device}");
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

    $page->screenshot(true, "character-mails-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);
