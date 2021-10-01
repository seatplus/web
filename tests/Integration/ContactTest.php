<?php


use Inertia\Testing\Assert;
use Seatplus\Eveapi\Models\Contacts\Contact;

test('see component', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contacts'));

    $response->assertInertia( fn (Assert $page) => $page->component('Character/Contact/Index'));
});

it('has details', function () {

    test()->test_character->contacts()->create(Contact::factory()->make()->toArray());

    $contact = test()->test_character->contacts->first();

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contacts.detail', test()->test_character->character_id));

    $response->assertOk();

});
