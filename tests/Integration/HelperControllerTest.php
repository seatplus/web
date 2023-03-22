<?php


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use function Pest\Laravel\get;

uses(\Seatplus\Web\Tests\Traits\MockRetrieveEsiDataAction::class);

it('stores resolved id to cache', function () {
    $id = test()->test_character->character_id;

    $esi_mock_return_data = [
       'id' => $id,
       'name' => test()->test_character->name,
       'category' => 'character',
   ];

    test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

    $result = test()->actingAs(test()->test_user)
       ->post(route('resolve.ids'), [$id]);

    $result->assertJson([
       $esi_mock_return_data,
   ]);

    $cache_value = cache(sprintf('name:%s', $id));
    expect($cache_value->name)->toEqual(test()->test_character->name);
});

it('returns cached value for resolved ids', function () {
    $id = test()->test_character->character_id;

    $cached_value = [
        'id' => $id,
        'name' => test()->test_character->name,
        'category' => 'character',
    ];

    cache([sprintf('name:%s', $id) => $cached_value], now()->addSeconds(2));

    $result = test()->actingAs(test()->test_user)
        ->post(route('resolve.ids'), [$id]);

    $result->assertJson([
        $cached_value,
    ]);
});

it('resolves character affiliation', function () {
    $id = test()->test_character->character_id;

    $esi_mock_return_data = [
        'alliance_id' => 123,
        'character_id' => 456,
        'corporation_id' => 789,
        'faction_id' => null,
    ];

    test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

    $result = test()->actingAs(test()->test_user)
        ->post(route('resolve.character_affiliation'), [$id]);

    $result->assertJson([
        $esi_mock_return_data,
    ]);
});

it('resolves corporation info', function () {
    $id = test()->test_character->corporation->corporation_id;

    $esi_mock_return_data = test()->test_character->corporation->toArray();

    test()->mockRetrieveEsiDataAction([$esi_mock_return_data]);

    $result = test()->actingAs(test()->test_user)
        ->get(route('resolve.corporation_info', $id));

    $result->assertJson([
        $esi_mock_return_data,
    ]);
});

test('one can search existing systems', function () {
    updateRefreshTokenWithScopes(test()->test_character->refresh_token, ['esi-search.search_structures.v1']);

    $system = System::factory()->create([
        'name' => 'jita',
    ]);

    System::factory()->count(4)->create();

    test()->actingAs(test()->test_user);

    get(route('autosuggestion.search', ['search' => 'J', 'categories' => ['system']]))
        ->assertInvalid(['search' => 'The search field must be at least 3 characters.']);

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn(
            test()->mockEsiResponse([
                'solar_system' => [
                    $system->system_id,
                ],
            ]),
            test()->mockEsiResponse([
                [
                    'id' => $system->system_id,
                    'name' => $system->name,
                    'category' => 'solar_system',
                ],
            ])
        );

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.search', ['search' => 'jit', 'categories' => ['system']]))
        ->assertOk();


    expect($result->original)->toHaveCount(1);
});

test('one can search existing region', function () {
    updateRefreshTokenWithScopes(test()->test_character->refresh_token, ['esi-search.search_structures.v1']);

    $region = Region::factory()->create([
        'name' => 'Delve',
    ]);

    Region::factory()->count(4)->create();

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.search', ['search' => 'D', 'categories' => ['region']]))
        ->assertInvalid(['search']);

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn(
            test()->mockEsiResponse([
                'region' => [
                    $region->region_id,
                ],
            ]),
            test()->mockEsiResponse([
                [
                    'id' => $region->region_id,
                    'name' => $region->name,
                    'category' => 'region',
                ],
            ])
        );

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.search', ['search' => 'Del', 'categories' => ['region']]))
        ->assertOk();

    expect($result->original)->toHaveCount(1);
});

test('one can get resource variants via http and cache', function () {
    Http::fake();

    $resource_type = 'types';
    $resource_id = 587;
    $url = "https://images.evetech.net/${resource_type}/${resource_id}";
    $expected_response = ["render", "icon"];

    Http::shouldReceive('get->json')->once()->andReturn(json_encode($expected_response));

    expect(cache($url))->toBeNull();

    // first time miss cache
    $result = test()->actingAs(test()->test_user)
        ->get(route('get.resource.variants', [
            'resource_type' => $resource_type,
            'resource_id' => $resource_id,
        ]))
        ->assertOk()
        ->assertJson($expected_response);

    expect(cache($url))->not()->toBeNull();
});

test('one can get market prices', function () {
    $container = new EsiRequestContainer(
        method: 'get',
        version: 'v1',
        endpoint: '/markets/prices/',
    );

    test()->mockRetrieveEsiDataAction([
        (object) [
            "adjusted_price" => 0,
            "average_price" => 31_214_609.93,
            "type_id" => 43691,
        ],
        (object) [ "adjusted_price" => 1_005_248.1289155,
            "average_price" => 1_002_393.46,
            "type_id" => 32772,
        ],
        (object) [ "adjusted_price" => 111879.41656101559,
            "average_price" => 104750.07,
            "type_id" => 32774,
        ],
    ]);

    expect(cache('market_prices'))->toBeNull();

    $result = test()->actingAs(test()->test_user)
        ->get(route('get.markets.prices'))
        ->assertOk();

    test()->assertNotNull(cache('market_prices'));
});

it('has auttosuggest for types, groups and categories', function () {
    $type = Type::factory()->create([
        'name' => 'TypeName',
        'group_id' => Group::factory()->create([
            'name' => 'GroupName',
            'category_id' => Category::factory()->create([
                'name' => 'CategoryName',
            ]),
        ]),
    ]);

    $terms = ['Typ', 'Grou', 'Cate'];

    foreach ($terms as $term) {
        $result = test()->actingAs(test()->test_user)
            ->get(route('autosuggestion.typesOrGroupOrCategories', ['search' => $term]))
            ->assertOk();

        expect($result->original)->toHaveCount(1);
    }
});
