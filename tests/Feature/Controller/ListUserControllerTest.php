<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

beforeEach(function () {
    Queue::fake();
});

it('denies ListUserController to unauthenticated user', function () {
    test()->get(route('list.users'))
        ->assertRedirect();
});

it('denies ListUserController without permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('list.users'))
        ->assertForbidden();
});

it('lists users for admin', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    $other_user = User::factory()->create();

    test()->actingAs(test()->test_user)
        ->get(route('list.users'))
        ->assertOk()
        ->assertJsonFragment(['id' => $other_user->id]);
});

it('searches users by character name', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->get(route('list.users', ['name' => test()->test_character->name]))
        ->assertOk()
        ->assertJsonFragment(['id' => test()->test_user->id]);
});

it('finds a user by any of their characters (an alt), returning the main for display', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    // Add an alt to the test user; the main stays test_character.
    $alt = CharacterInfo::factory()->create(['name' => 'Alt Toon']);
    CharacterUser::create([
        'user_id' => test()->test_user->getKey(),
        'character_id' => $alt->character_id,
        'character_owner_hash' => sha1((string) $alt->character_id),
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('list.users', ['name' => 'Alt Toon']))
        ->assertOk()
        // matched via the alt, but the user is returned…
        ->assertJsonFragment(['id' => test()->test_user->id])
        // …with the user's MAIN character for the pill/row (camelCase mainCharacter)…
        ->assertJsonPath('data.0.mainCharacter.character_id', test()->test_user->main_character_id)
        // …and the alt's name available for the picker subtext.
        ->assertJsonFragment(['name' => 'Alt Toon']);
});
