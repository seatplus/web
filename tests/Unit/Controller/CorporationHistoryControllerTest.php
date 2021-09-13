<?php


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class);

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
});

test('one can corporation history endpoint', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.history', test()->test_character->character_id))
        ->assertOk();
});
