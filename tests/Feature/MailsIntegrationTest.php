<?php

use Inertia\Testing\AssertableInertia as Assert;

test('see component', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.mails'));

    $response->assertInertia(fn (Assert $page) => $page->component('Character/Mail/Index'));
});

test('get mail headers of secondary user', function () {
    if (test()->test_user->can('superuser')) {
        test()->test_user->removeRole('superuser');

        // now re-register all the roles and permissions
    }

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.mail.headers', ['character_ids' => [test()->test_character->character_id]]));

    $response->assertOk();
});
