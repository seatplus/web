<?php


use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    test()->test_character->roles()->update(['roles' => ['']]);
});

test('has dispatchable job', function () {
    test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertUnauthorized();




    test()->assignPermissionToTestUser('view member tracking');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->has('dispatchTransferObject'));
});
