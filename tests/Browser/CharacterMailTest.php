<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
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
 * Character mail browser test — the mail header list (desktop aside + mobile) is now
 * an Inertia <InfiniteScroll> over the `mailHeaders` scroll prop instead of the axios
 * InfiniteLoadingHelper. Seeds 40 mails addressed to the logged-in character (6h apart)
 * and asserts scrolling the list merges the next page in. No permission needed —
 * own-character access. Provisioning helper: tests/Browser/Pest.php.
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
