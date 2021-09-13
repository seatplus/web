<?php


use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Illuminate\Support\Facades\Http;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Web\Services\GetNamesFromIdsService;
use Seatplus\Web\Tests\TestCase;


it('stores resolved id to cache', function () {

   $id = test()->test_character->character_id;

   $esi_mock_return_data = [
       'id' => $id,
       'name' => test()->test_character->name,
       'category' => 'character'
   ];

   test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

   $result = test()->actingAs(test()->test_user)
       ->post(route('resolve.ids'), [$id]);

   $result->assertJson([
       $esi_mock_return_data
   ]);

   $cache_value = cache(sprintf('name:%s', $id));
   expect($cache_value->name)->toEqual(test()->test_character->name);
});

it('returns cached value for resolved ids', function () {

    $id = test()->test_character->character_id;

    $cached_value = [
        'id' => $id,
        'name' => test()->test_character->name,
        'category' => 'character'
    ];

    cache([sprintf('name:%s', $id) => $cached_value], now()->addSeconds(2));

    $result = test()->actingAs(test()->test_user)
        ->post(route('resolve.ids'), [$id]);

    $result->assertJson([
        $cached_value
    ]);
});

it('resolves character affiliation', function () {

    $id = test()->test_character->character_id;

    $esi_mock_return_data = [
        'alliance_id' => 123,
        'character_id' => 456,
        'corporation_id' => 789,
        'faction_id' => null
    ];

    test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

    $result = test()->actingAs(test()->test_user)
        ->post(route('resolve.character_affiliation'), [$id]);

    $result->assertJson([
        $esi_mock_return_data
    ]);
});

it('resolves corporation info', function () {

    $id = test()->test_character->corporation->corporation_id;

    $esi_mock_return_data = test()->test_character->corporation->toArray();

    test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

    $result = test()->actingAs(test()->test_user)
        ->get(route('resolve.corporation_info', $id));

    $result->assertJson([
        $esi_mock_return_data
    ]);
});

/**
 * @runInSeparateProcess
 * @preserveGlobalState disabled
 */
test('one can search for solar systems', function () {

    $system = System::factory()->make([
        'name' => 'test'
    ]);

    $esi_mock_return_data = [
        'solar_system' => [$system->system_id],
    ];

    test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

    // Mock GetNamesFromIdsService - we don't test that it's tested otherwise
    $mock = Mockery::mock('overload:' . GetNamesFromIdsService::class);
    $mock->shouldReceive([$system->system_id])
        ->andReturn([
            'category' => 'solar_system',
            'id' => $system->system_id,
            'faction_id' => $system->name
        ]);

    $result = test()->actingAs(test()->test_user)
        ->get(route('resolve.solar_system', 'tes'))
        ->assertOk();

});

test('one can search existing systems', function () {


    $system = System::factory()->create([
        'name' => 'jita'
    ]);

    System::factory()->count(4)->create();

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.system', ['search' => '']))
        ->assertForbidden();

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn(
            test()->mockEsiResponse([
                'solar_system' => [
                    $system->system_id
                ]
            ]),
            test()->mockEsiResponse([
                [
                    'id' => $system->system_id,
                    'name' => $system->name,
                    'category' => 'solar_system'
                ]
            ])
        );

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.system', ['search' =>'jit']))
        ->assertOk();


    expect($result->original)->toHaveCount(1);
});

test('one can search existing region', function () {
    $region = Region::factory()->create([
        'name' => 'Delve'
    ]);

    Region::factory()->count(4)->create();

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.region', ['search' => '']))
        ->assertForbidden();

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn(
            test()->mockEsiResponse([
                'region' => [
                    $region->region_id
                ]
            ]),
            test()->mockEsiResponse([
                [
                    'id' => $region->region_id,
                    'name' => $region->name,
                    'category' => 'region'
                ]
            ])
        );

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.region', ['search' =>'Del']))
        ->assertOk();

    expect($result->original)->toHaveCount(1);
});

test('on can get resource variants', function () {
    Http::fake();

    $expected_response = ["render", "icon"];

    Http::shouldReceive('get->json')->once()->andReturn(json_encode($expected_response));

    $result = test()->actingAs(test()->test_user)
        ->get(route('get.resource.variants', [
            'resource_type' => 'types',
            'resource_id' => 587
        ]))
        ->assertOk()
        ->assertJson($expected_response);

});

test('one can get market prices', function () {

    $container = new EsiRequestContainer([
        'method' => 'get',
        'version' => 'v1',
        'endpoint' => '/markets/prices/'
    ]);

    test()->mockRetrieveEsiDataAction([
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

    expect(cache('market_prices'))->toBeNull();

    $result = test()->actingAs(test()->test_user)
        ->get(route('get.markets.prices'))
        ->assertOk();

    test()->assertNotNull(cache('market_prices'));
});
