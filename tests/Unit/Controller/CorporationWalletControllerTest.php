<?php


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
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

test('one can call journal endpoint', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.wallet_journal.detail', [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'division_id' => 1,
        ]))
        ->assertOk();
});

test('one can call transaction endpoint', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.wallet_transaction.detail', [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'division_id' => 1,
        ]))
        ->assertOk();
});

test('on get ballance records from last30 days', function () {
    WalletJournal::factory()->count(1)->create([
        'wallet_journable_id' => test()->test_character->corporation->corporation_id,
        'division' => 1,
        'date' => now()->subDays(29),
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.balance', [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'division_id' => 1,
        ]));

    $response->assertOk();

    expect(data_get($response->original->toArray(), 'data'))->toHaveCount(1);
});

test('on get ballance records from before30 days', function () {
    WalletJournal::factory()->count(1)->create([
        'wallet_journable_id' => test()->test_character->corporation->corporation_id,
        'division' => 1,
        'date' => now()->subDays(33),
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.balance', [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'division_id' => 1,
        ]));

    $response->assertOk();

    expect(data_get($response->original->toArray(), 'data'))->toHaveCount(1);
});
