<?php

use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\EsiClient\EsiClient;
use Seatplus\EsiSchema\Contracts\EsiRawResponse;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

it('has scope settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('settings.scopes'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

test('one can create sso setting', function () {
    $corporation = CorporationInfo::factory()->make();

    $mock = Mockery::mock(EsiClient::class);
    $mock->shouldReceive('assertScope')->andReturnNull();
    $mock->shouldReceive('invoke')->andReturn(
        new EsiRawResponse(data: (object) $corporation->attributesToArray(), isCachedLoad: false, pages: 1)
    );
    app()->instance(EsiClient::class, $mock);

    expect(SsoScopes::where('morphable_id', (string) $corporation->corporation_id)->first())
        ->toBeNull();

    $response = test()->actingAs(test()->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        'corporation_id' => $corporation->corporation_id,
                        'id' => $corporation->corporation_id,
                        'name' => 'Amok.',
                        'category' => 'corporation',
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

test('one can delete sso setting', function () {
    $corporation = CorporationInfo::factory()->make();

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
                        'id' => $corporation->corporation_id,
                        'name' => 'Amok.',
                        'category' => 'corporation',
                    ],
                ],
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'default',
            ]
        );

    Bus::assertDispatched(CorporationInfoJob::class);

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
