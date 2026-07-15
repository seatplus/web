<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, array|string $url, array $options = []): mixed
    {
        $page = visit($url, $options);

        return $device === 'iphone' ? $page->on()->iPhone15() : $page;
    }
}

/*
 * Authentication browser tests — run against the real assembled core app (never under
 * web's Testbench harness; web's phpunit.xml excludes tests/Browser). They execute
 * both in core's browser CI (against the released web package) and in a web PR's
 * browser job (which clones core and overlays the branch). visit()/assertNoSmoke()/
 * screenshot() come from pest-plugin-browser, provided by the core app; user
 * provisioning comes from the suite helpers in tests/Browser/Pest.php.
 *
 * Ordered unauthenticated first, then authenticated.
 */

uses(RefreshDatabase::class);

if (! function_exists('attachOwnedCharacter')) {
    /**
     * Attach an additional owned character to the same user that owns $existing, so a
     * browser test can exercise the multi-character case — the dashboard renders a card
     * per every character the logged-in user owns, not just the main. Returns the newly
     * attached character. Guarded so each Browser test file can define it standalone
     * without colliding when the suite loads several.
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

/* ------------------------------------------------------------- unauthenticated */

it('renders the login page', function (string $device) {
    $page = deviceVisit($device, '/login');

    $page->assertNoSmoke();
    $page->assertSee('Sign in');
    $page->screenshot(true, "login-{$device}");
})->with(['desktop', 'iphone']);

/* --------------------------------------------------------------- authenticated */

it('renders the dashboard for an authenticated user', function (string $device) {
    actingAsCharacter();

    $page = deviceVisit($device, '/home');

    $page->assertNoSmoke();
    $page->assertSee('Characters');

    // Let the lazy-loaded portraits (EveImage IntersectionObserver) settle so the
    // screenshot captures them rather than the placeholder SVGs.
    $page->wait(1);
    $page->screenshot(true, "dashboard-{$device}");

    test()->assertAuthenticated();
})->with(['desktop', 'iphone']);

it('renders a dashboard card for every character the user owns', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    $page = deviceVisit($device, '/home');

    $page->assertNoSmoke();
    $page->waitForText('Characters');

    // Portraits lazy-load via EveImage's IntersectionObserver — let them attach.
    $page->wait(1);

    // The dashboard renders one card (h-12 w-12 portrait) per owned character, not just
    // the main. Assert both specific portraits resolve, and that exactly two character
    // portraits render — a main-only dashboard would show a single one.
    foreach ([$mainCharacter, $secondCharacter] as $character) {
        $page->assertAttributeContains(
            "img.h-12.w-12[src*='characters/{$character->character_id}/portrait']",
            'src',
            "images.evetech.net/characters/{$character->character_id}/portrait",
        );
    }

    $page->assertCount('img.h-12.w-12', 2);

    $page->screenshot(true, "dashboard-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);

it('wires the character portrait to the EVE image server', function (string $device) {
    $character = actingAsCharacter();

    $page = deviceVisit($device, '/home');

    // EveImage lazy-loads the portrait via an IntersectionObserver, mounting the
    // <img> a beat after the page settles — wait for it before asserting.
    $page->waitForText('Characters');
    $page->wait(1);

    // Scope to the dashboard character-card portrait (h-12 w-12) so the locator
    // resolves to exactly one element — the character also appears as the sidebar
    // avatar (h-9 w-9), and a multi-match locator trips Playwright's strict mode.
    // assertAttributeContains waits for the (lazy) element to attach.
    //
    // We assert the *URL wiring* (correct CCP images.evetech.net portrait endpoint),
    // not that the bytes load — the factory character id has no real portrait on
    // Tranquility, so assertNoBrokenImages would be a false negative here.
    $selector = "img.h-12.w-12[src*='characters/{$character->character_id}/portrait']";

    $page->assertAttributeContains(
        $selector,
        'src',
        "images.evetech.net/characters/{$character->character_id}/portrait",
    );
})->with(['desktop', 'iphone']);
