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

    // EveImage lazy-loads the portrait via an IntersectionObserver, so wait for the
    // dashboard to render before inspecting the <img>. We assert the *URL wiring*
    // (correct CCP images.evetech.net portrait endpoint), not that the bytes load —
    // the factory character id has no real portrait on Tranquility, so
    // assertNoBrokenImages would be a false negative here.
    //
    // The selector encodes the assertion (host + character + portrait variant) and
    // assertPresent counts matches, so it tolerates the character appearing more
    // than once (e.g. the sidebar avatar at size=64 and the dashboard card at 512).
    $selector = "img[src*='images.evetech.net/characters/{$character->character_id}/portrait']";

    $page = visit('/home');
    $page->waitForText('Characters');

    $page->assertPresent($selector);
});
