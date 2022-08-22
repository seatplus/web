<?php


use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Models\Asset\Asset;

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
    '/location/{location_id}' => 'location.assets',
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
            'search' => substr(test()->test_character->name, 5),
        ]));
    //->assertOk();

    $response->assertOk();
});

test('load asset in system', function () {
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
            'location_flag' => 'Hangar',
        ]);

    $system = $asset->location->locatable->system;

    // call without filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
        ]))
        ->assertOk();

    expect($response->original)->toHaveCount(1);

    // call with system_id filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'systems' => $system->system_id,
        ]));

    expect($response->original)->toHaveCount(1);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'systems' => [$system->system_id],
        ]));

    expect($response->original)->toHaveCount(1);

    // call with system_id + 1 filter and expect no assets to be found
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'systems' => $system->system_id + 1,
        ]));

    expect($response->original)->toHaveCount(0);
});

test('load asset in region', function () {
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
            'location_flag' => 'Hangar',
        ]);

    $region = $asset->location->locatable->system->region;


    // call without filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
        ]))
        ->assertOk();

    expect($response->original)->toHaveCount(1);

    // call with system_id filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'regions' => $region->region_id,
        ]));

    expect($response->original)->toHaveCount(1);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'regions' => [$region->region_id],
        ]));

    expect($response->original)->toHaveCount(1);

    // call with system_id + 1 filter and expect no assets to be found
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'systems' => $region->region_id + 1,
        ]));

    expect($response->original)->toHaveCount(0);
});

test('load asset in unknown location', function () {
    // 1. create asset with location
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
            'location_flag' => 'Hangar',
        ]);

    test()->assertNotNull($asset->location);

    // 2. create asset without location (unknown)
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => 12345,
            'location_flag' => 'Hangar',
        ]);

    expect($asset->location)->toBeNull();
    expect($asset->manual_location)->toBeNull();

    // 3. call normally
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
        ]));

    // 4. expect 2 assets
    expect($response->original)->toHaveCount(2);

    // 5. call only unknown locations
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
            'withUnknownLocations' => true,
        ]));

    // 6. expect only one
    expect($response->original)->toHaveCount(1);

    // call with unknown locations
});

test('load asset on watchlist', function () {
    // Prepare
    $asset = Asset::factory()
        ->count(2)
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_flag' => 'Hangar',
        ])->first();


    $content = Asset::factory()
        ->create([
            'location_id' => $asset->item_id,
            'location_flag' => 'Cargo',
            'type_id' => Type::factory()->create([
                'group_id' => Group::factory()->create(['category_id' => Category::factory()]),
            ]),
        ]);

    // Act
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.character.assets.locations', [
            'character_ids' => [test()->test_character->character_id],
        ]));

    // we expect a total of 3 assets
    expect(Asset::all())->toHaveCount(3);
    // only two should be in the result
    expect($response->original)->toHaveCount(2);

    $tests = [
        ['types' => [$content->type_id]],
        ['groups' => [$content->type->group_id]],
        ['categories' => [$content->type->group->category_id]],
    ];

    foreach ($tests as $test) {
        $payload = array_merge($test, [
            'character_ids' => [test()->test_character->character_id],
        ]);

        $watchlist_response = test()->actingAs(test()->test_user)
            ->get(route('get.character.assets.locations', $payload));

        expect($watchlist_response->original)->toHaveCount(1);

        $watchlist_location_response = test()->actingAs(test()->test_user)
            ->get(route('location.assets', ['location_id' => $asset->location_id, ...$payload]));

        expect($watchlist_location_response->original)->toHaveCount(1);
    }
});
