<?php


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;


beforeEach(function () {
    test()->test_character->roles()->update(['roles' => ['']]);
});

test('has dispatchable job', function () {

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertForbidden();


    $permission = Permission::findOrCreate('view member tracking');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('corporation.member_tracking'))
        ->assertOk();

    $response->assertInertia( fn (Assert $page) => $page->has('dispatchTransferObject'));
});
