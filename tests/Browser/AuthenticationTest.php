<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Web\Models\Recruitment\Enlistment;

require_once __DIR__.'/helpers.php';

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

/* ------------------------------------------------------------- unauthenticated */

it('renders the login page', function (string $device) {
    $page = deviceVisit($device, '/login');

    $page->assertNoSmoke();
    $page->assertSee('Sign in');
    snap($page, "login-{$device}");
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
    snap($page, "dashboard-{$device}");

    test()->assertAuthenticated();
})->with(['desktop', 'iphone']);

it('no longer shows the job postings section on the dashboard', function (string $device) {
    $character = actingAsCharacter();

    // An open posting for the character's corporation would previously have surfaced here as a
    // "Job Postings" card; open postings now live solely in the Job Portal.
    Enlistment::query()->create([
        'corporation_id' => $character->corporation->corporation_id,
        'type' => 'user',
    ]);

    $page = deviceVisit($device, '/home');

    $page->assertNoSmoke();
    $page->waitForText('Characters');
    $page->assertDontSee('Job Postings');

    snap($page, "dashboard-no-job-postings-{$device}");
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

    snap($page, "dashboard-multiple-characters-{$device}");
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
