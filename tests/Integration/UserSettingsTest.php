<?php


use Inertia\Testing\Assert;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\CharacterUser;

it('has user settings', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('user.settings'));

    //$response->assertInertia('Configuration/UserSettings');
    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/UserSettings'));
});

test('one can update main character', function () {
    $secondary_character = Event::fakeFor(fn() => CharacterUser::factory()->make());

    test()->test_user->character_users()->save($secondary_character);

    test()->assertNotEquals(test()->test_user->main_character, $secondary_character->character);

    test()->actingAs(test()->test_user)
        ->json('POST', route('change.main_character'), [
            "character_id" => $secondary_character->character_id,
        ]);

    expect($secondary_character->character)->toEqual(test()->test_user->refresh()->main_character);

});
