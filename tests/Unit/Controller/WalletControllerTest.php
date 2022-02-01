<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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
        ->get(route('character.wallets'));

    $response->assertInertia(
        fn (Assert $page) => $page
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
        'date'                => now()->subDays(29),
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.balance', test()->test_character->character_id));

    $response->assertOk();

    expect(data_get($response->original->toArray(), 'data'))->toHaveCount(1);
});

test('on get ballance records from before30 days', function () {
    WalletJournal::factory()->count(1)->create([
        'wallet_journable_id' => test()->test_character->character_id,
        'date'                => now()->subDays(33),
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.balance', test()->test_character->character_id));

    $response->assertOk();

    expect(data_get($response->original->toArray(), 'data'))->toHaveCount(1);
});

test('one can call corporation wallet endpoint', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.wallet', [
            'corporation_ids' => [test()->test_character->corporation->corporation_id],
        ]))
        ->assertOk();
});
