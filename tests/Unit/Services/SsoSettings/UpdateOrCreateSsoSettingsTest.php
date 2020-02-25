<?php


namespace Seatplus\Web\Tests\Unit\Services;


use Mockery;
use Seatplus\Eveapi\Actions\Jobs\Alliance\AllianceInfoAction;
use Seatplus\Eveapi\Actions\Jobs\Corporation\CorporationInfoAction;
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
            'selectedCorpOrAlliance' => [
                'alliance_id' => 1354830081,
                'id' =>1354830081,
                'name' => "Goonswarm Federation"
            ],
            'selectedScopes' => [
                'character' => ["esi-assets.read_assets.v1,esi-universe.read_structures.v1"],
                'corporation' => []
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
            'selectedCorpOrAlliance' => [
                'corporation_id' => 1184675423,
                'id' =>1184675423,
                'name' => "Amok."
            ],
            'selectedScopes' => [
                'character' => ["esi-assets.read_assets.v1,esi-universe.read_structures.v1"],
                'corporation' => []
            ]
        ];

        (new UpdateOrCreateSsoSettings($request))->execute();
    }

    /** @test */
    public function it_creates_sso_settings()
    {
        $mock = Mockery::mock('overload:' . CorporationInfoAction::class);
        $mock->shouldReceive('execute')
            ->once()
            ->andReturn(null);

        $request = [
            'selectedCorpOrAlliance' => [
                'corporation_id' => 1184675423,
                'id' => 1184675423,
                'name' => "Amok."
            ],
            'selectedScopes' => [
                'character' => ["esi-assets.read_assets.v1,esi-universe.read_structures.v1"],
                'corporation' => []
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
