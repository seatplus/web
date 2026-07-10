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
        ->get(route('character.wallets'));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Character/Wallet/Index')
            ->has('dispatchTransferObject')
    );
});

test('one can call corporation wallet endpoint', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.wallet', [
            'corporation_ids' => [test()->test_character->corporation->corporation_id],
        ]))
        ->assertOk();
});
