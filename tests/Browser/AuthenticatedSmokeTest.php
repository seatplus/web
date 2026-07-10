<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Models\Onboarding;

/*
 * Authenticated dashboard smoke test — runs against the real assembled core app.
 *
 * Provisions a user with a single controlled character and loads /home, asserting
 * the Dashboard actually renders (visible "Characters" heading) with no JS/console
 * errors — mirroring a real logged-in user.
 *
 * We create ONE CharacterInfo (its factory builds the affiliation/refresh-token/
 * role) and link it as the main character, rather than User::factory() — that
 * builds a two-character graph whose CharacterAffiliation ids intermittently
 * collide, and a characterless user renders the dashboard blank. Queue is faked so
 * character-creation model events don't dispatch real ESI jobs. Factory-name
 * guessing for the seatplus packages is set in core's tests/TestCase::setUp.
 */

uses(RefreshDatabase::class);

it('renders the dashboard for an authenticated user', function () {
    Queue::fake();

    $character = CharacterInfo::factory()->create();

    $user = new User();
    $user->main_character_id = $character->character_id;
    $user->save();

    CharacterUser::create([
        'user_id' => $user->getKey(),
        'character_id' => $character->character_id,
        'character_owner_hash' => sha1((string) $character->character_id),
    ]);

    Onboarding::create(['user_id' => $user->getKey()]);

    $this->actingAs($user);

    $page = visit('/home');

    $page->assertNoSmoke();
    $page->assertSee('Characters');
    $page->screenshot(true, 'dashboard');

    $this->assertAuthenticated();
});
