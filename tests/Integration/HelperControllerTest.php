<?php


namespace Seatplus\Web\Tests\Integration;

use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Illuminate\Support\Facades\Http;
use Mockery;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Web\Services\GetNamesFromIdsService;
use Seatplus\Web\Tests\TestCase;

class HelperControllerTest extends TestCase
{

    /** @test */
    public function itStoresResolvedIdToCache() {

       $id = $this->test_character->character_id;

       $esi_mock_return_data = [
           'id' => $id,
           'name' => $this->test_character->name,
           'category' => 'character'
       ];

       $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

       $result = $this->actingAs($this->test_user)
           ->post(route('resolve.ids'), [$id]);

       $result->assertJson([
           $esi_mock_return_data
       ]);

       $cache_value = cache(sprintf('name:%s', $id));
       $this->assertEquals($this->test_character->name, $cache_value->name);
    }

    /** @test */
    public function itReturnsCachedValueForResolvedIds() {

        $id = $this->test_character->character_id;

        $cached_value = [
            'id' => $id,
            'name' => $this->test_character->name,
            'category' => 'character'
        ];

        cache([sprintf('name:%s', $id) => $cached_value], now()->addSeconds(2));

        $result = $this->actingAs($this->test_user)
            ->post(route('resolve.ids'), [$id]);

        $result->assertJson([
            $cached_value
        ]);
    }

    /** @test */
    public function itResolvesCharacterAffiliation() {

        $id = $this->test_character->character_id;

        $esi_mock_return_data = [
            'alliance_id' => 123,
            'character_id' => 456,
            'corporation_id' => 789,
            'faction_id' => null
        ];

        $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

        $result = $this->actingAs($this->test_user)
            ->post(route('resolve.character_affiliation'), [$id]);

        $result->assertJson([
            $esi_mock_return_data
        ]);
    }

    /** @test */
    public function itResolvesCorporationInfo() {

        $id = $this->test_character->corporation->corporation_id;

        $esi_mock_return_data = $this->test_character->corporation->toArray();

        $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

        $result = $this->actingAs($this->test_user)
            ->get(route('resolve.corporation_info', $id));

        $result->assertJson([
            $esi_mock_return_data
        ]);
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function oneCanSearchForSolarSystems() {

        $system = System::factory()->make([
            'name' => 'test'
        ]);

        $esi_mock_return_data = [
            'solar_system' => [$system->system_id],
        ];

        $this->mockRetrieveEsiDataAction([$esi_mock_return_data]);

        // Mock GetNamesFromIdsService - we don't test that it's tested otherwise
        $mock = Mockery::mock('overload:' . GetNamesFromIdsService::class);
        $mock->shouldReceive([$system->system_id])
            ->andReturn([
                'category' => 'solar_system',
                'id' => $system->system_id,
                'faction_id' => $system->name
            ]);

        $result = $this->actingAs($this->test_user)
            ->get(route('resolve.solar_system', 'tes'))
            ->assertOk();

    }

    /** @test */
    public function oneCanSearchExistingSystems()
    {


        $system = System::factory()->create([
            'name' => 'jita'
        ]);

        System::factory()->count(4)->create();

        $result = $this->actingAs($this->test_user)
            ->get(route('autosuggestion.system', ['search' => '']))
            ->assertForbidden();

        RetrieveEsiData::shouldReceive('execute')
            ->twice()
            ->andReturn(
                $this->mockEsiResponse([
                    'solar_system' => [
                        $system->system_id
                    ]
                ]),
                $this->mockEsiResponse([
                    [
                        'id' => $system->system_id,
                        'name' => $system->name,
                        'category' => 'solar_system'
                    ]
                ])
            );

        $result = $this->actingAs($this->test_user)
            ->get(route('autosuggestion.system', ['search' =>'jit']))
            ->assertOk();


        $this->assertCount(1, $result->original);
    }

    /** @test */
    public function oneCanSearchExistingRegion()
    {
        $region = Region::factory()->create([
            'name' => 'Delve'
        ]);

        Region::factory()->count(4)->create();

        $result = $this->actingAs($this->test_user)
            ->get(route('autosuggestion.region', ['search' => '']))
            ->assertForbidden();

        RetrieveEsiData::shouldReceive('execute')
            ->twice()
            ->andReturn(
                $this->mockEsiResponse([
                    'region' => [
                        $region->region_id
                    ]
                ]),
                $this->mockEsiResponse([
                    [
                        'id' => $region->region_id,
                        'name' => $region->name,
                        'category' => 'region'
                    ]
                ])
            );

        $result = $this->actingAs($this->test_user)
            ->get(route('autosuggestion.region', ['search' =>'Del']))
            ->assertOk();

        $this->assertCount(1, $result->original);
    }

    /** @test  */
    public function onCanGetResourceVariants()
    {
        Http::fake();

        $expected_response = ["render", "icon"];

        Http::shouldReceive('get->json')->once()->andReturn(json_encode($expected_response));

        $result = $this->actingAs($this->test_user)
            ->get(route('get.resource.variants', [
                'resource_type' => 'types',
                'resource_id' => 587
            ]))
            ->assertOk()
            ->assertJson($expected_response);

    }

    /** @test */
    public function oneCanGetMarketPrices()
    {

        $container = new EsiRequestContainer([
            'method' => 'get',
            'version' => 'v1',
            'endpoint' => '/markets/prices/'
        ]);

        $this->mockRetrieveEsiDataAction([
            (object) [
                "adjusted_price"=> 0,
                "average_price" => 31214609.93,
                "type_id" => 43691
            ],
            (object) [ "adjusted_price"=> 1005248.1289154688,
                "average_price"=> 1002393.46,
                "type_id"=> 32772
            ],
            (object) [ "adjusted_price"=> 111879.41656101559,
                "average_price"=> 104750.07,
                "type_id"=> 32774
            ]
        ]);

        $this->assertNull(cache('market_prices'));

        $result = $this->actingAs($this->test_user)
            ->get(route('get.markets.prices'))
            ->assertOk();

        $this->assertNotNull(cache('market_prices'));
    }

}
