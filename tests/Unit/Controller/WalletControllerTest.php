<?php


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;


beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
});

test('has dispatchable job', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.wallets'));

    $response->assertInertia( fn (Assert $page) => $page
        ->component('Character/Wallet/Index')
        ->has('dispatchTransferObject')
    );
});

test('one can call journal endpoint', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.wallet_journal.detail', test()->test_character->character_id))
        ->assertOk();
});

test('one can call transaction endpoint', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.wallet_transaction.detail', test()->test_character->character_id))
        ->assertOk();
});

test('on get ballance records from last30 days', function () {
    WalletJournal::factory()->count(1)->create([
        'wallet_journable_id' => test()->test_character->character_id,
        'date' => now()->subDays(29)
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.balance', test()->test_character->character_id));

    $response->assertOk();

    expect(data_get($response->original->toArray(), 'data'))->toHaveCount(1);
});

test('on get ballance records from before30 days', function () {
    WalletJournal::factory()->count(1)->create([
        'wallet_journable_id' => test()->test_character->character_id,
        'date' => now()->subDays(33)
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.balance', test()->test_character->character_id));

    $response->assertOk();

    expect(data_get($response->original->toArray(), 'data'))->toHaveCount(1);
});

test('one can call corporation wallet endpoint', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.wallet', [
            'corporation_ids' => [test()->test_character->corporation->corporation_id]
        ]))
        ->assertOk();
});
