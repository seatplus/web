<?php


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
                'id' =>1354830081,
                'name' => "Goonswarm Federation",
                'category' => 'alliance'
            ]
        ],
        'selectedScopes' => [
            "esi-assets.read_assets.v1,esi-universe.read_structures.v1",
            'publicData'
        ],
        'type' => 'default'
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
                'id' =>1184675423,
                'name' => "Amok.",
                'type' => 'corporation'
            ],

        ],
        'selectedScopes' => [
            "esi-assets.read_assets.v1,esi-universe.read_structures.v1",
            'publicData'
        ],
        'type' => 'default'
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
                'id' =>1184675423,
                'name' => "Amok.",
                'category' => 'corporation'
            ],
        ],
        'selectedScopes' => [
            "esi-assets.read_assets.v1,esi-universe.read_structures.v1",
            'publicData'
        ],
        'type' => 'default'
    ];

    \Pest\Laravel\assertDatabaseMissing('sso_scopes', [
        'morphable_id' => 1184675423
    ]);

    (new UpdateOrCreateSsoSettings($request))->execute();

    \Pest\Laravel\assertDatabaseHas('sso_scopes', [
        'morphable_id' => 1184675423
    ]);
});
