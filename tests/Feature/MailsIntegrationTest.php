<?php

use Inertia\Testing\AssertableInertia as Assert;

test('see component', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('character.mails'));

    $response->assertInertia(fn (Assert $page) => $page->component('Character/Mail/Index'));
});
