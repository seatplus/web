<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Models\Onboarding;

/*
 * Authenticated dashboard smoke tests — run against the real assembled core app.
 *
 * Provisions a user with a single controlled character and loads /home, mirroring
 * a real logged-in user.
 *
 * We create ONE CharacterInfo (its factory builds the affiliation/refresh-token/
 * role) and link it as the main character, rather than User::factory() — that
 * builds a two-character graph whose CharacterAffiliation ids intermittently
 * collide, and a characterless user renders the dashboard blank. Queue is faked so
 * character-creation model events don't dispatch real ESI jobs. Factory-name
 * guessing for the seatplus packages is set in core's tests/TestCase::setUp.
 */

uses(RefreshDatabase::class);

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
    $page->screenshot(true, 'dashboard');

    test()->assertAuthenticated();
});

it('wires the character portrait to the EVE image server', function () {
    $character = provisionAuthenticatedUser();

    // EveImage lazy-loads the portrait via an IntersectionObserver, so we need an
    // assertion that waits for the <img> to mount — assertAttributeContains does
    // (getAttribute retries until the element is attached); a plain snapshot count
    // races the observer and finds nothing.
    //
    // Scope to the dashboard character-card portrait (h-12 w-12) so the locator
    // resolves to exactly one element — the character also appears as the sidebar
    // avatar (h-9 w-9), and a multi-match locator trips Playwright's strict mode.
    //
    // We assert the *URL wiring* (correct CCP images.evetech.net portrait endpoint),
    // not that the bytes load — the factory character id has no real portrait on
    // Tranquility, so assertNoBrokenImages would be a false negative here.
    $selector = "img.h-12.w-12[src*='characters/{$character->character_id}/portrait']";

    $page = visit('/home');
    $page->waitForText('Characters');

    $page->assertAttributeContains(
        $selector,
        'src',
        "images.evetech.net/characters/{$character->character_id}/portrait",
    );
});
