<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

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

it('renders the login page', function () {
    $page = visit('/login');

    $page->assertNoSmoke();
    $page->assertSee('Sign in');
    $page->screenshot(true, 'login');
});

/* --------------------------------------------------------------- authenticated */

it('renders the dashboard for an authenticated user', function () {
    actingAsCharacter();

    $page = visit('/home');

    $page->assertNoSmoke();
    $page->assertSee('Characters');

    // Let the lazy-loaded portraits (EveImage IntersectionObserver) settle so the
    // screenshot captures them rather than the placeholder SVGs.
    $page->wait(1);
    $page->screenshot(true, 'dashboard');

    test()->assertAuthenticated();
});

it('wires the character portrait to the EVE image server', function () {
    $character = actingAsCharacter();

    $page = visit('/home');

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
});
