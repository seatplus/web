<?php


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Contracts\ContractItem;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

test('has dispatchable job', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contracts'));

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Character/Contract/Index')
        ->has('dispatchTransferObject')
    );
});

test('one get contracts per character', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contracts.details', test()->test_character->character_id))
        ->assertOk();
});

test('one can call transaction endpoint', function () {
    $contract_item = ContractItem::factory()->count(5)->create([
        'contract_id' => \Seatplus\Eveapi\Models\Contracts\Contract::factory(),
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('contract.details', [
            'character_id' => test()->test_character->character_id,
            'contract_id' => 1234,
        ]))
        ->assertOk();

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Character/Contract/ContractDetails')
        ->has('contract')
    );
});
