<?php


use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    test()->test_character->roles()->update(['roles' => ['']]);
});

test('has dispatchable job', function () {
    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertUnauthorized();


    $permission = Permission::findOrCreate('view member tracking');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->has('dispatchTransferObject'));
});
