<?php

use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\CharacterUser;

it('has user settings', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('user.settings'));

    // $response->assertInertia('Configuration/UserSettings');
    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/UserSettings'));
});

test('one can update main character', function () {
    $secondary_character = Event::fakeFor(fn () => CharacterUser::factory()->make());

    $this->test_user->characterUsers()->save($secondary_character);

    $this->assertNotEquals($this->test_user->mainCharacter, $secondary_character->character);

    $this->actingAs($this->test_user);

    $response = $this->put(route('change.main_character', [
        'new_character_id' => $secondary_character->character_id,
    ]));

    $response->assertRedirect();

    $user = $this->test_user->refresh();

    expect($secondary_character->character)->toEqual($user->mainCharacter);
});
