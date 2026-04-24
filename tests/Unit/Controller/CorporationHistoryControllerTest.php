<?php

use Seatplus\Auth\Models\Permissions\Permission;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);
});

test('one can corporation history endpoint', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.history', test()->test_character->character_id))
        ->assertOk();
});
