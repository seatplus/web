<?php


namespace Seatplus\Web\Tests\Unit\Services;


use Mockery;
use Seatplus\Eveapi\Actions\Jobs\Alliance\AllianceInfoAction;
use Seatplus\Eveapi\Actions\Jobs\Corporation\CorporationInfoAction;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Tests\TestCase;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

class UpdateOrCreateSsoSettingsTest extends TestCase
{
    /** @test */
    public function it_calls_alliance_info_action()
    {
         $mock = Mockery::mock('overload:' . AllianceInfoAction::class);
         $mock->shouldReceive('execute')
            ->once()
            ->andReturn(null);

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
            ]
        ];

        (new UpdateOrCreateSsoSettings($request))->execute();
    }

    /** @test */
    public function it_calls_corporation_info_action()
    {
        $mock = Mockery::mock('overload:' . CorporationInfoAction::class);
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn(null);

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
            ]
        ];

        (new UpdateOrCreateSsoSettings($request))->execute();

        $this->assertCount(3, SsoScopes::where('morphable_id', 1184675423)->first()->selected_scopes);
    }

    /** @test */
    public function it_creates_sso_settings()
    {
        $mock = Mockery::mock('overload:' . CorporationInfoAction::class);
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn(null);

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
            ]
        ];

        $this->assertDatabaseMissing('sso_scopes', [
            'morphable_id' => 1184675423
        ]);

        (new UpdateOrCreateSsoSettings($request))->execute();

        $this->assertDatabaseHas('sso_scopes', [
            'morphable_id' => 1184675423
        ]);
    }

}
