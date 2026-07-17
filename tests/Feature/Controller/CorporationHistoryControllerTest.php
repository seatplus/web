<?php

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Character\CorporationHistory;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

test('one can corporation history endpoint', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.history', test()->test_character->character_id))
        ->assertOk();
});

test('corporation history is returned as an un-paginated collection', function () {
    CorporationHistory::factory()->count(3)->create([
        'character_id' => test()->test_character->character_id,
    ]);

    // Another character's history must not leak into the response.
    CorporationHistory::factory()->create();

    test()->actingAs(test()->test_user)
        ->get(route('corporation.history', test()->test_character->character_id))
        ->assertOk()
        // A top-level JSON array (3 entries), not a paginator envelope ({ data, links, meta }).
        ->assertJsonCount(3)
        ->assertJsonMissingPath('data')
        ->assertJsonMissingPath('links')
        ->assertJsonMissingPath('meta')
        ->assertJsonStructure([
            '*' => ['record_id', 'corporation_id', 'start_date'],
        ]);
});
