<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    $this->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

test('has dispatchable job', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('character.wallets'));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Character/Wallet/Index')
            ->has('dispatchTransferObject')
    );
});

test('one can call corporation wallet endpoint', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('corporation.wallet', [
            'corporation_ids' => [$this->test_character->corporation->corporation_id],
        ]))
        ->assertOk();
});
