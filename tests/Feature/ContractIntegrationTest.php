<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Contracts\ContractItem;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    $this->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

test('has dispatchable job', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('character.contracts'));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Character/Contract/Index')
            ->has('dispatchTransferObject')
    );
});

test('one can call transaction endpoint', function () {
    $contract_item = ContractItem::factory()->count(5)->create([
        'contract_id' => Contract::factory(),
    ]);

    $response = $this->actingAs($this->test_user)
        ->get(route('contract.details', [
            'character_id' => $this->test_character->character_id,
            'contract_id' => 1234,
        ]))
        ->assertOk();

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Character/Contract/ContractDetails')
            ->has('contract')
    );
});
