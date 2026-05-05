<?php

declare(strict_types=1);

use Seatplus\Web\Tests\Traits\MockRetrieveEsiDataAction;

uses(MockRetrieveEsiDataAction::class);

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

it('searches affiliatable with ESI query', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    $token = test()->test_character->refresh_token;
    updateRefreshTokenWithScopes($token, ['esi-search.search_structures.v1']);

    test()->mockRetrieveEsiDataAction(['character' => [test()->test_character->character_id]]);

    test()->actingAs(test()->test_user)
        ->get(route('acl.search.affiliatable', ['query' => test()->test_character->name]))
        ->assertOk()
        ->assertJsonFragment([
            'id' => test()->test_character->character_id,
            'category' => 'character',
        ]);
});
