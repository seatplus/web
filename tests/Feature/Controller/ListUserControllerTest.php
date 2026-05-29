<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\User;

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
