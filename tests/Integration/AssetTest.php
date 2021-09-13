<?php


use Inertia\Testing\Assert;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Web\Models\Asset\Asset;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('is protected by authentication', function () {

    $response = test()->followingRedirects()
        ->get(route('character.assets'));

    $response->assertInertia( fn (Assert $page) => $page->component('Auth/Login'));
});

test('see component', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.assets'));

    $response->assertInertia( fn (Assert $page) => $page->component('Character/Assets'));
});

test('load asset', function () {

    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets'));

    $response->assertOk();
});

it('has asset prop', function () {

    $character_assets = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class
    ]);

    // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
    //test()->app->instance('path.public', __DIR__ .'/../../src/public');

    $response = test()->actingAs(test()->test_user)
        ->get(route('character.assets'));

    //dd($response->exception->getMessage());

    $response->assertInertia( fn (Assert $page) => $page->has('dispatchTransferObject'));
});

it('has list affiliated character list route', function () {
    Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.affiliated.characters','assets'));
        //->assertOk();

    $response->assertOk();
});

test('load asset in system', function () {
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
            'location_flag' => 'Hangar'
        ]);

    $system = $asset->location->locatable->system;

    // call without filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets'))
        ->assertOk();

    expect($response->original)->toHaveCount(1);

    // call with system_id filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'systems' => $system->system_id
        ]));

    expect($response->original)->toHaveCount(1);

    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'systems' => [$system->system_id]
        ]));

    expect($response->original)->toHaveCount(1);

    // call with system_id + 1 filter and expect no assets to be found
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'systems' => $system->system_id+1
        ]));

    expect($response->original)->toHaveCount(0);
});

test('load asset in region', function () {
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
            'location_flag' => 'Hangar'
        ]);

    $region = $asset->location->locatable->system->region;


    // call without filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets'))
        ->assertOk();

    expect($response->original)->toHaveCount(1);

    // call with system_id filter
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'regions' => $region->region_id
        ]));

    expect($response->original)->toHaveCount(1);

    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'regions' => [$region->region_id]
        ]));

    expect($response->original)->toHaveCount(1);

    // call with system_id + 1 filter and expect no assets to be found
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'systems' => $region->region_id+1
        ]));

    expect($response->original)->toHaveCount(0);
});

test('load asset in unknown location', function () {

    // 1. create asset with location
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
            'location_flag' => 'Hangar'
        ]);

    test()->assertNotNull($asset->location);

    // 2. create asset without location (unknown)
    $asset = Asset::factory()
        ->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => 12345,
            'location_flag' => 'Hangar'
        ]);

    expect($asset->location)->toBeNull();
    expect($asset->manual_location)->toBeNull();

    // 3. call normally
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets'));

    // 4. expect 2 assets
    expect($response->original)->toHaveCount(2);

    // 5. call only unknown locations
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets', [
            'withUnknownLocations' => true
        ]));

    // 6. expect only one
    expect($response->original)->toHaveCount(1);

    // call with unknown locations
});
