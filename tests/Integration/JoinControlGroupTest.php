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

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::find($role->id);

    //dd(test()->test_character)

    /*CharacterAffiliation::factory()->create([
        'character_id' => test()->test_character->character_id
    ]);*/

    /*CharacterUser::factory()->create([
        'user_id' => test()->test_user->id,
        'character_id' => test()->test_character->character_id
    ]);*/

    test()->test_character = test()->test_character->refresh();
});

test('user can join waitlist', function () {
    expect(test()->role->acl_affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'on-request',
                'affiliations' => [
                    [
                        'type' => 'corporation',
                        'id' => test()->test_character->corporation->corporation_id,
                    ],
                ],
                'members' => [],
            ],
        ]);

    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeFalse();

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.join'), [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id,
        ]);

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    expect(test()->role->acl_members()->whereStatus('waitlist')->first()->user_id)->toEqual(test()->test_user->id);
});

test('superuser can join immediately', function () {
    expect(test()->role->acl_affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['superuser']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'on-request',
                'affiliations' => [
                    [
                        'type' => 'corporation',
                        'id' => test()->test_character->corporation->corporation_id,
                    ],
                ],
                'members' => [],
            ],
        ]);

    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeFalse();

    expect(test()->test_user->roles->isNotEmpty())->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.join'), [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id,
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    expect(test()->role->members()->first()->user_id)->toEqual(test()->test_user->id);
});

// Helpers
