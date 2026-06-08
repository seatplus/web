<?php

use Illuminate\Support\Facades\Http;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;

use function Pest\Laravel\get;

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

test('esi search validates the search term length', function () {
    test()->actingAs(test()->test_user);

    get(route('autosuggestion.search', ['search' => 'J', 'categories' => ['system']]))
        ->assertInvalid(['search' => 'The search field must be at least 3 characters.']);
});

test('one can get resource variants via http and cache', function () {
    Http::fake();

    $resource_type = 'types';
    $resource_id = 587;
    $url = "https://images.evetech.net/{$resource_type}/{$resource_id}";
    $expected_response = ['render', 'icon'];

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

test('one can get cached market prices without hitting esi', function () {
    $prices = collect([
        (object) ['adjusted_price' => 0, 'average_price' => 31_214_609.93, 'type_id' => 43691],
    ]);

    cache(['market_prices' => $prices], now()->addDay());

    test()->actingAs(test()->test_user)
        ->get(route('get.markets.prices'))
        ->assertOk()
        ->assertJsonFragment(['type_id' => 43691]);
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
