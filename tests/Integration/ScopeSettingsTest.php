<?php


use Illuminate\Support\Facades\Bus;
use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Esi\Jobs\Corporation\CorporationInfoAction;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;


beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
});

it('has scope settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('settings.scopes'));


    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
test('one can create sso setting', function () {
    $mock = Mockery::mock('overload:' . CorporationInfoAction::class);
    $mock->shouldReceive('execute')
        ->once()
        ->andReturn(null);

    test()->assertDatabaseMissing('sso_scopes',[
        'morphable_id' => 1184675423
    ]);


    $response = test()->actingAs(test()->test_user)
        ->post(route('create.scopes'),
            [
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
                ],
                'type' => 'default'
            ]
        );

    test()->assertDatabaseHas('sso_scopes',[
        'morphable_id' => 1184675423
    ]);
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
test('one can delete sso setting', function () {
    /*$mock = Mockery::mock('overload:' . CorporationInfoAction::class);
    $mock->shouldReceive('execute')
        ->once()
        ->andReturn(null);*/

    test()->assertDatabaseMissing('sso_scopes',[
        'morphable_id' => 1184675423
    ]);

    Bus::fake();

    $response = test()->actingAs(test()->test_user)
        ->post(route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        'corporation_id' => 1184675423,
                        'id' =>1184675423,
                        'name' => "Amok.",
                        'category' => 'corporation'
                    ],
                ],
                'selectedScopes' => [
                    "esi-assets.read_assets.v1,esi-universe.read_structures.v1"
                ],
                'type' => 'default'
            ]
        );

    Bus::assertDispatched(CorporationInfoJob::class);

    test()->assertDatabaseHas('sso_scopes',[
        'morphable_id' => 1184675423
    ]);

    $response = test()->actingAs(test()->test_user)
        ->delete(route('delete.scopes', 1184675423));

    test()->assertDatabaseMissing('sso_scopes',[
        'morphable_id' => 1184675423
    ]);
});

test('one can create and delete global sso setting', function () {

    expect(setting('global_sso_scopes'))->toBeNull();

    $response = test()->actingAs(test()->test_user)
        ->post(route('create.scopes'),
            [
                'selectedScopes' => [
                    "esi-assets.read_assets.v1,esi-universe.read_structures.v1"
                ],
                'type' => 'global'
            ]
        );

    test()->assertNotNull(SsoScopes::global()->first());

    $response = test()->actingAs(test()->test_user)
        ->delete(route('delete.scopes', null));
});
