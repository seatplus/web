<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Models\Onboarding;

/*
 * Authentication browser tests — run against the real assembled core app (never under
 * web's Testbench harness; web's phpunit.xml excludes tests/Browser). They execute
 * both in core's browser CI (against the released web package) and in a web PR's
 * browser job (which clones core and overlays the branch). visit()/assertNoSmoke()/
 * screenshot() come from pest-plugin-browser, provided by the core app.
 *
 * Ordered unauthenticated first, then authenticated. Provisioning uses ONE
 * CharacterInfo linked as the main character rather than User::factory(), whose
 * two-character graph has intermittently-colliding CharacterAffiliation ids and
 * renders the dashboard blank. Queue is faked so character-creation events don't
 * dispatch real ESI jobs. Factory-name guessing is set in core's tests/TestCase.
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

function provisionAuthenticatedUser(): CharacterInfo
{
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

    test()->actingAs($user);

    return $character;
}

it('renders the dashboard for an authenticated user', function () {
    provisionAuthenticatedUser();

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
    $character = provisionAuthenticatedUser();

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
