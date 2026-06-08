<?php

declare(strict_types=1);

it('denies SearchAffiliatableController to unauthenticated user', function () {
    test()->get(route('acl.search.affiliatable'))
        ->assertRedirect();
});

it('denies SearchAffiliatableController without permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable'))
        ->assertForbidden();
});

it('returns affiliatable entities', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable'))
        ->assertOk()
        ->assertJsonFragment([
            'id' => test()->test_character->character_id,
            'category' => 'character',
        ]);
});
