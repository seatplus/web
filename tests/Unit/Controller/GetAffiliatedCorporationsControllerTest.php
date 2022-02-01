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

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('can accept or deny applications');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

it('get affiliated corporations', function () {
    \Seatplus\Eveapi\Models\Character\CharacterRole::updateOrCreate([
        'character_id' => test()->test_character->character_id,
    ], [
        'roles' => ['Director'],
    ]);

    expect(test()->test_character->roles)
        ->hasRole('roles', 'Director')->toBeTrue()
        ->hasRole('roles', 'Accountant')->toBeTrue();

    WalletJournal::factory()->count(5)->create([
        'wallet_journable_type' => CorporationInfo::class,
        'wallet_journable_id' => test()->test_character->corporation->corporation_id,
    ]);

    expect(CorporationInfo::find(test()->test_character->corporation->corporation_id))
        ->not()->toBeNull()
        ->wallet_journals->toHaveCount(5);

    $response = test()->actingAs(test()->test_user->refresh())
        ->get(route('get.affiliated.corporations', [
            'permission' => 'wallet_journals',
            'corporation_role' => 'Accountant|Junior_Accountant',
            'search' => substr(test()->test_character->corporation->name, 5),
        ]));

    $response->assertOk();

    expect($response->original)->toHaveCount(1);
});
