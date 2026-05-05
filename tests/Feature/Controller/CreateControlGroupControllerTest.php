<?php

declare(strict_types=1);

it('denies CreateControlGroupController to unauthenticated user', function () {
    test()->postJson(route('acl.create'), ['name' => 'test'])
        ->assertUnauthorized();
});

it('denies CreateControlGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.create'), ['name' => 'test'])
        ->assertForbidden();

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});

it('creates a control group', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);

    test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->postJson(route('acl.create'), ['name' => 'test']);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'test']);
});
