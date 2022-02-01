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

use Illuminate\Support\Facades\Bus;
use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\SsoScopes;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

it('has scope settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('settings.scopes'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
test('one can create sso setting', function () {
    $corporation = \Seatplus\Eveapi\Models\Corporation\CorporationInfo::factory()->make();

    $response = new \Seatplus\EsiClient\DataTransferObjects\EsiResponse(
        json_encode($corporation->attributesToArray()),
        [],
        11,
        200
    );

    \Seatplus\Eveapi\Services\Facade\RetrieveEsiData::shouldReceive('execute')
        ->andReturn($response);

    expect(SsoScopes::where('morphable_id', (string) $corporation->corporation_id)->first())
        ->toBeNull();

    $response = test()->actingAs(test()->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        'corporation_id' => $corporation->corporation_id,
                        'id'             => $corporation->corporation_id,
                        'name'           => 'Amok.',
                        'type'           => 'corporation',
                    ],
                ],
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'default',
            ]
        );

    expect(SsoScopes::where('morphable_id', (string) $corporation->corporation_id)->first())
        ->not()->toBeNull()
        ->toBeInstanceOf(SsoScopes::class);
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
test('one can delete sso setting', function () {
    $corporation = \Seatplus\Eveapi\Models\Corporation\CorporationInfo::factory()->make();

    $response = new \Seatplus\EsiClient\DataTransferObjects\EsiResponse(
        json_encode($corporation->attributesToArray()),
        [],
        11,
        200
    );

    \Seatplus\Eveapi\Services\Facade\RetrieveEsiData::shouldReceive('execute')
        ->andReturn($response);

    expect(SsoScopes::where('morphable_id', (string) $corporation->corporation_id)->first())
        ->toBeNull();

    Bus::fake();

    $response = test()->actingAs(test()->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        'corporation_id' => $corporation->corporation_id,
                        'id'             => $corporation->corporation_id,
                        'name'           => 'Amok.',
                        'type'           => 'corporation',
                    ],
                ],
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'default',
            ]
        );

    Bus::assertDispatched(CorporationInfoJob::class);

    /*\Pest\Laravel\assertDatabaseHas('sso_scopes',[
        'morphable_id' => $corporation->corporation_id
    ]);*/
    expect(SsoScopes::where('morphable_id', (string) $corporation->corporation_id)->first())
        ->not()->toBeNull()
        ->toBeInstanceOf(SsoScopes::class);

    $response = test()->actingAs(test()->test_user)
        ->delete(route('delete.scopes', $corporation->corporation_id));

    expect(SsoScopes::where('morphable_id', (string) $corporation->corporation_id)->first())->toBeNull();
});

test('one can create and delete global sso setting', function () {
    expect(setting('global_sso_scopes'))->toBeNull();

    $response = test()->actingAs(test()->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'global',
            ]
        );

    test()->assertNotNull(SsoScopes::global()->first());

    $response = test()->actingAs(test()->test_user)
        ->delete(route('delete.scopes', null));
});
