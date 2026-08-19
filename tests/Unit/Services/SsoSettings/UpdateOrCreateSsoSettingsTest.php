<?php

use Illuminate\Support\Facades\Bus;
use Seatplus\Eveapi\Jobs\Alliances\AllianceInfoJob;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

it('calls alliance info action', function () {
    Bus::fake();

    $request = [
        'selectedEntities' => [
            [
                'alliance_id' => 1_354_830_081,
                'id' => 1_354_830_081,
                'name' => 'Goonswarm Federation',
                'category' => 'alliance',
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

it('calls corporation info action', function () {
    Bus::fake();

    $request = [
        'selectedEntities' => [
            [
                'corporation_id' => 1_184_675_423,
                'id' => 1_184_675_423,
                'name' => 'Amok.',
                'category' => 'corporation',
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

    expect(SsoScopes::where('morphable_id', 1_184_675_423)->first()->selected_scopes)->toHaveCount(3);
});

it('creates sso settings', function () {
    Bus::fake();

    $request = [
        'selectedEntities' => [
            [
                'corporation_id' => 1_184_675_423,
                'id' => 1_184_675_423,
                'name' => 'Amok.',
                'category' => 'corporation',
            ],
        ],
        'selectedScopes' => [
            'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
            'publicData',
        ],
        'type' => 'default',
    ];

    \Pest\Laravel\assertDatabaseMissing('sso_scopes', [
        'morphable_id' => 1_184_675_423,
    ]);

    (new UpdateOrCreateSsoSettings($request))->execute();

    \Pest\Laravel\assertDatabaseHas('sso_scopes', [
        'morphable_id' => 1_184_675_423,
    ]);
});

it('does not hijack the row of another morphable type sharing the id', function () {
    Bus::fake();

    // The eveapi factory defaults selected_scopes to a JSON string, which the array cast would
    // double-encode — pass a real array.
    $alliance_scopes = SsoScopes::factory()->create([
        'morphable_id' => 1_184_675_423,
        'morphable_type' => AllianceInfo::class,
        'type' => 'user',
        'selected_scopes' => ['esi-skills.read_skills.v1'],
    ]);

    (new UpdateOrCreateSsoSettings([
        'selectedEntities' => [
            [
                'id' => 1_184_675_423,
                'category' => 'corporation',
            ],
        ],
        'selectedScopes' => ['esi-assets.read_assets.v1'],
        'type' => 'default',
    ]))->execute();

    expect(SsoScopes::query()->where('morphable_id', 1_184_675_423)->count())->toBe(2)
        ->and($alliance_scopes->fresh()->type)->toBe('user')
        ->and($alliance_scopes->fresh()->selected_scopes)->toBe(['esi-skills.read_skills.v1']);
});

it('keeps the installation-wide list on its own row', function () {
    Bus::fake();

    (new UpdateOrCreateSsoSettings([
        'selectedEntities' => [],
        'selectedScopes' => ['esi-assets.read_assets.v1'],
        'type' => 'global',
    ]))->execute();

    $row = SsoScopes::query()->whereNull('morphable_id')->sole();

    expect($row->type)->toBe('global')
        ->and($row->selected_scopes)->toBe(['esi-assets.read_assets.v1']);

    // Saving again updates that row rather than adding a second one.
    (new UpdateOrCreateSsoSettings([
        'selectedEntities' => [],
        'selectedScopes' => ['esi-skills.read_skills.v1'],
        'type' => 'global',
    ]))->execute();

    expect(SsoScopes::query()->whereNull('morphable_id')->count())->toBe(1)
        ->and(SsoScopes::query()->whereNull('morphable_id')->sole()->selected_scopes)
        ->toBe(['esi-skills.read_skills.v1']);
});
