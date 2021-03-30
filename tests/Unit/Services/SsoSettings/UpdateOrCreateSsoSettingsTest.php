<?php


namespace Seatplus\Web\Tests\Unit\Services\SsoSettings;


use Illuminate\Support\Facades\Bus;
use Mockery;
use Seatplus\Eveapi\Jobs\Alliances\AllianceInfoJob;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Tests\TestCase;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

class UpdateOrCreateSsoSettingsTest extends TestCase
{
    /** @test */
    public function it_calls_alliance_info_action()
    {
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
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_calls_corporation_info_action()
    {
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

        (new UpdateOrCreateSsoSettings($request))->execute();

        Bus::assertDispatched(CorporationInfoJob::class);

        $this->assertCount(3, SsoScopes::where('morphable_id', 1184675423)->first()->selected_scopes);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function it_creates_sso_settings()
    {
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

        $this->assertDatabaseMissing('sso_scopes', [
            'morphable_id' => 1184675423
        ]);

        (new UpdateOrCreateSsoSettings($request))->execute();

        $this->assertDatabaseHas('sso_scopes', [
            'morphable_id' => 1184675423
        ]);
    }

}
