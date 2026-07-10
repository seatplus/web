<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

test('has dispatchable job', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.wallet'));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Corporation/Wallets/Wallet')
            ->has('dispatchTransferObject')
    );
});
