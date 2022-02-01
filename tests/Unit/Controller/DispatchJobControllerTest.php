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

use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Web\Jobs\ManualDispatchedJob;

beforeEach(function () {
    test()->dispatch_transfer_object = [
        'manual_job'                => array_search(ContactHydrateBatch::class, config('web.jobs')),
        'permission'                => config('eveapi.permissions.'.Contact::class),
        'required_scopes'           => config('eveapi.scopes.character.contacts'),
        'required_corporation_role' => '',
    ];
});

it('dispatches job', function () {
    $mock = Mockery::mock('overload:'.ManualDispatchedJob::class);
    $mock->shouldReceive('handle')->andReturn(1);
    $mock->shouldReceive('setName')->andReturn($mock);
    $mock->shouldReceive('setJobs')->andReturn($mock);

    $dispatch_transfer_object = test()->dispatch_transfer_object;

    $character_id = test()->test_character->character_id;

    $cache_key = sprintf('%s:%s', $dispatch_transfer_object['manual_job'], $character_id);

    expect(cache($cache_key))->toBeNull();

    $response = test()->actingAs(test()->test_user)
        ->post(route('dispatch.job'), [
            'character_id'             => $character_id,
            'dispatch_transfer_object' => $dispatch_transfer_object,
        ]);

    test()->assertNotNull(cache($cache_key));
});

test('one get dispatchable entities', function () {
    updateRefreshTokenWithScopes(test()->test_character->refresh_token, test()->dispatch_transfer_object['required_scopes']);

    $response = test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), test()->dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson([
            [
                'character_id' => test()->test_character->character_id,
                'name'         => test()->test_character->name,
                'batch'        => ['state' => 'ready'],
            ],
        ]);
});
