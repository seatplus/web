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
use Seatplus\Eveapi\Jobs\Alliances\AllianceInfoJob;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

it('calls alliance info action', function () {
    /*$mock = Mockery::mock('overload:' . AllianceInfoAction::class);
    $mock->shouldReceive('execute')
       ->once()
       ->andReturn(null);*/

    Bus::fake();

    $request = [
        'selectedEntities' => [
            [
                'alliance_id' => 1354830081,
                'id'          => 1354830081,
                'name'        => 'Goonswarm Federation',
                'type'        => 'alliance',
            ],
        ],
        'selectedScopes' => [
            'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
            'publicData',
        ],
        'type' => 'default',
    ];

    (new UpdateOrCreateSsoSettings($request))->execute();

    Bus::assertDispatched(AllianceInfoJob::class);
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
it('calls corporation info action', function () {
    Bus::fake();

    $request = [
        'selectedEntities' => [
            [
                'corporation_id' => 1184675423,
                'id'             => 1184675423,
                'name'           => 'Amok.',
                'type'           => 'corporation',
            ],

        ],
        'selectedScopes' => [
            'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
            'publicData',
        ],
        'type' => 'default',
    ];

    (new UpdateOrCreateSsoSettings($request))->execute();

    Bus::assertDispatched(CorporationInfoJob::class);

    expect(SsoScopes::where('morphable_id', 1184675423)->first()->selected_scopes)->toHaveCount(3);
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
it('creates sso settings', function () {
    Bus::fake();

    $request = [
        'selectedEntities' => [
            [
                'corporation_id' => 1184675423,
                'id'             => 1184675423,
                'name'           => 'Amok.',
                'type'           => 'corporation',
            ],
        ],
        'selectedScopes' => [
            'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
            'publicData',
        ],
        'type' => 'default',
    ];

    \Pest\Laravel\assertDatabaseMissing('sso_scopes', [
        'morphable_id' => 1184675423,
    ]);

    (new UpdateOrCreateSsoSettings($request))->execute();

    \Pest\Laravel\assertDatabaseHas('sso_scopes', [
        'morphable_id' => 1184675423,
    ]);
});
