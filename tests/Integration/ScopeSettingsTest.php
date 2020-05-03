<?php


namespace Seatplus\Web\Tests\Integration;


use Mockery;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Actions\Jobs\Corporation\CorporationInfoAction;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class ScopeSettingsTest extends TestCase
{
    public function setUp(): void
    {

        parent::setUp();

        $permission = Permission::findOrCreate('superuser');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function it_has_scope_settings()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('settings.scopes'));

        $response->assertComponent('Configuration/Scopes/OverviewScopeSettings');
    }

    /** @test */
    public function one_can_create_sso_setting()
    {
        $mock = Mockery::mock('overload:' . CorporationInfoAction::class);
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn(null);

        $this->assertDatabaseMissing('sso_scopes',[
            'morphable_id' => 1184675423
        ]);


        $response = $this->actingAs($this->test_user)
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
                        'character' => ["esi-assets.read_assets.v1,esi-universe.read_structures.v1"],
                        'corporation' => []
                    ]
                ]
            );

        $this->assertDatabaseHas('sso_scopes',[
            'morphable_id' => 1184675423
        ]);
    }

    /** @test */
    public function one_can_delete_sso_setting()
    {
        $mock = Mockery::mock('overload:' . CorporationInfoAction::class);
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn(null);

        $this->assertDatabaseMissing('sso_scopes',[
            'morphable_id' => 1184675423
        ]);

        $response = $this->actingAs($this->test_user)
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
                        'character' => ["esi-assets.read_assets.v1,esi-universe.read_structures.v1"],
                        'corporation' => []
                    ]
                ]
            );

        $this->assertDatabaseHas('sso_scopes',[
            'morphable_id' => 1184675423
        ]);

        $response = $this->actingAs($this->test_user)
            ->delete(route('delete.settings.scopes',1184675423));

        $this->assertDatabaseMissing('sso_scopes',[
            'morphable_id' => 1184675423
        ]);
    }

}
