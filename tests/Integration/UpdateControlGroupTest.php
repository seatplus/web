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
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

test('on can update role type', function () {

    //dd(test()->test_user->hasRole('test'));

    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'automatic',
                'affiliations' => [],
                'members' => [],
            ],
        ]);

    expect(test()->role->fresh()->type)->toEqual('automatic');
});

test('manual control group adds member', function () {
    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'manual',
                'affiliations' => [],
                'members' => [
                    [
                        'id' => test()->test_user->id,
                        'user' => test()->test_user,
                    ],
                ],
            ],
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();
});

test('manual control group removes member', function () {
    test()->role->activateMember(test()->test_user);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'manual',
                'affiliations' => [],
                'members' => [],
            ],
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('automatic control group adds affiliation', function () {
    expect(test()->role->acl_affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'automatic',
                'affiliations' => [
                    [
                        'type' => 'corporation',
                        'id' => CorporationInfo::factory()->make()->corporation_id,
                    ],
                ],
                'members' => [],
            ],
        ]);

    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeFalse();
});

test('automatic control group removes affiliation', function () {
    expect(test()->role->acl_affiliations->isEmpty())->toBeTrue();

    test()->role->acl_affiliations()->create([
        'affiliatable_id' => CorporationInfo::factory()->make()->corporation_id,
        'affiliatable_type' => CorporationInfo::class,
    ]);

    test()->assertFalse(test()->role->refresh()->acl_affiliations->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'automatic',
                'affiliations' => [],
                'members' => [],
            ],
        ]);

    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeTrue();
});

test('on request control group adds and removes moderators', function () {
    expect(test()->role->moderators->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual('manual');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'on-request',
                'moderators' => [
                    [
                        'id' => test()->test_user->id,
                    ],
                ],
            ],
        ]);

    // Test if test user is moderator
    expect(test()->role->refresh()->moderators->isNotEmpty())->toBeTrue();
    expect(test()->role->refresh()->isModerator(test()->test_user))->toBeTrue();

    // assert that no affiliations has been created
    expect(test()->role->refresh()->acl_affiliations->isEmpty())->toBeTrue();
});

// Helpers
