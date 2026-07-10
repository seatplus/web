<?php

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Models\Onboarding;

/*
 * Shared helpers for the browser test suite. This file lives alongside the tests and
 * is mirrored into core's tests/Browser/web by browser-tests.php, where core's Pest
 * runner auto-loads it — so provisioning stays here and the tests stay behaviour-focused.
 */

/**
 * Provision a logged-in user owning a single controlled character and return it.
 *
 * We create ONE CharacterInfo (its factory builds the affiliation/refresh-token/role)
 * linked as the main character rather than User::factory(), whose two-character graph
 * has intermittently-colliding CharacterAffiliation ids and renders pages blank. Queue
 * is faked so character-creation model events don't dispatch real ESI jobs. Factory-name
 * guessing for the seatplus packages is registered in core's tests/TestCase::setUp.
 */
function actingAsCharacter(): CharacterInfo
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

/**
 * Give a character an in-game corporation role (Director by default), which grants the
 * owning user corp-scoped access. CharacterInfo::factory() already creates an empty
 * CharacterRole, so update it in place.
 */
function giveCorporationRole(CharacterInfo $character, string $role = 'Director'): void
{
    CharacterRole::updateOrCreate(
        ['character_id' => $character->character_id],
        ['roles' => [$role]],
    );
}
