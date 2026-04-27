<?php

use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;

test('is protected by authentication', function () {
    $response = test()->followingRedirects()
        ->get(route('character.assets'));

    $response->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

test('see component', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.assets'));

    $response->assertInertia(fn (Assert $page) => $page->component('Character/Assets'));
});

test('requires character_ids parameter', function (string $route) {
    $response = test()->actingAs(test()->test_user)
        ->get(route($route, 1));

    $response->assertStatus(403);
})->with([
    '/locations' => 'get.character.assets.locations',
]);

test('load asset', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
        ]));

    $response->assertOk();
});

it('has asset prop', function () {
    $character_assets = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.assets'));

    $response->assertInertia(
        fn (Assert $page) => $page
            ->has('dispatchTransferObject')
            ->has('characterIds')
    );
});

it('has list affiliated character list route', function () {
    Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.affiliated.characters', [
            'permission' => 'assets',
            'search' => substr((string) test()->test_character->name, 5),
        ]));
    // ->assertOk();

    $response->assertOk();
});

test('load asset in unknown location', function () {

    // Arrange
    $unknown_location = Location::factory()->create();
    $known_location = Location::factory()->for(Station::factory(), 'locatable')->create();

    foreach ([$known_location, $unknown_location] as $location) {
        Asset::factory()
            ->create([
                'assetable_id' => test()->test_character->character_id,
                'location_id' => $location->location_id,
                'location_flag' => 'Hangar',
            ]);
    }

    // Act

    // 3. call normally
    $response_all = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
        ]))
        ->assertOk();

    // 5. call only unknown locations
    $response_only_unknown = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'only_unknown_locations' => true,
        ]))
        ->assertOk();

    // Assert
    expect($response_all->original)->toHaveCount(2)
        ->and($response_only_unknown->original)->toHaveCount(1);

});

test('load asset on watchlist', function () {

    // Arrange
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();

    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => $location->location_id,
            'location_flag' => 'Hangar',
        ]);

    $content = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => $asset->item_id,
        ]);

    $content_content = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => $content->item_id,
            'type_id' => Type::factory(),
            'group_id' => Group::factory(),
            'category_id' => Category::factory(),
        ]);

    $propertyMapping = [
        'systems' => [$location->locatable->system->system_id],
        'regions' => [$location->locatable->system->region->region_id],
        'types' => [$asset->type_id],
        'groups' => [$content->group_id],
        'categories' => [$content_content->category_id],
    ];

    foreach ($propertyMapping as $key => $value) {
        // Act
        $response = test()->actingAs(test()->test_user)
            ->get(route('get.character.assets.locations', [
                'character_ids' => [test()->test_character->character_id],
                $key => $value,
            ]))
            ->assertOk();

        // Assert
        expect($response->original)->toHaveCount(1);

        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->count('data', 1)
                ->has(
                    'data.0',
                    fn ($json) => $json
                        ->where('location_id', $location->location_id)
                        ->etc()
                )
                ->etc()
        );
    }
});
