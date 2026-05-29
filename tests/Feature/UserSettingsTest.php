<?php

use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\CharacterUser;

it('has user settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('user.settings'));

    // $response->assertInertia('Configuration/UserSettings');
    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/UserSettings'));
});

test('one can update main character', function () {
    $secondary_character = Event::fakeFor(fn () => CharacterUser::factory()->make());

    test()->test_user->characterUsers()->save($secondary_character);

    test()->assertNotEquals(test()->test_user->mainCharacter, $secondary_character->character);

    test()->actingAs(test()->test_user);

    $response = test()->put(route('change.main_character', [
        'new_character_id' => $secondary_character->character_id,
    ]));

    $response->assertRedirect();

    $user = test()->test_user->refresh();

    expect($secondary_character->character)->toEqual($user->mainCharacter);
});
