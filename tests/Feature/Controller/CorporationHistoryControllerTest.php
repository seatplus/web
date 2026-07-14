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

test('corporation history endpoint returns the full history unpaginated', function () {
    // More than one default page (15) to prove the response is not paginated.
    CorporationHistory::factory()->count(20)->create([
        'character_id' => test()->test_character->character_id,
    ]);

    test()->actingAs(test()->test_user)
        ->getJson(route('corporation.history', test()->test_character->character_id))
        ->assertOk()
        ->assertJsonCount(20);
});
