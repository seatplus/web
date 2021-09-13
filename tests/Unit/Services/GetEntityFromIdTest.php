<?php


use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Faker\Generator;
use Seat\Eseye\Containers\EsiResponse;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetEntityFromId;
use Seatplus\Web\Services\GetNamesFromIdsService;
use Seatplus\Web\Tests\TestCase;


test('happy path', function () {
    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $expected_result = [
        'id' => $character_affiliation->character_id,
        'character_id' => $character_affiliation->character_id,
        'name' => $character_affiliation->character->name,
        'corporation' => [
            'name' => $character_affiliation->corporation->name
        ],
        'alliance' => [
            'name' => $character_affiliation->alliance->name
        ]
    ];

    $cache_key =  sprintf('entityById:%s', $character_affiliation->character_id);
    expect(cache($cache_key))->toBeNull();

    $service = new GetEntityFromId($character_affiliation->character_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);

    expect(cache($cache_key))->toEqual($expected_result);
});

test('happy path without alliance', function () {
    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $character_affiliation->alliance_id = null;
    $character_affiliation->save();

    $expected_result = [
        'id' => $character_affiliation->character_id,
        'character_id' => $character_affiliation->character_id,
        'name' => $character_affiliation->character->name,
        'corporation' => [
            'name' => $character_affiliation->corporation->name
        ]
    ];

    $service = new GetEntityFromId($character_affiliation->character_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});

test('happy path via corporation id', function () {
    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $expected_result = [
        'id' => $character_affiliation->corporation_id,
        'corporation_id' => $character_affiliation->corporation_id,
        'name' => $character_affiliation->corporation->name,
        'alliance' => [
            'name' => $character_affiliation->alliance->name
        ]
    ];

    $service = new GetEntityFromId($character_affiliation->corporation_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});

test('happy path via alliance id', function () {

    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $expected_result = [
        'id' => $character_affiliation->alliance_id,
        'alliance_id' => $character_affiliation->alliance_id,
        'name' => $character_affiliation->alliance->name
    ];

    $service = new GetEntityFromId($character_affiliation->alliance_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});

test('unknown character id', function () {

    $alliance = AllianceInfo::factory()->create([
        'alliance_id' => 99000000123
    ]);

    $corporation = CorporationInfo::factory()->create([
        'alliance_id' => $alliance->alliance_id
    ]);
    $character = CharacterInfo::factory()->make([
        'corporation_id' => $corporation->corporation_id
    ]);

    $esi_mock_return_data = [
        'alliance_id' => $alliance->alliance_id,
        'character_id' => $character->character_id,
        'corporation_id' => $corporation->corporation_id,
        'faction_id' => null
    ];

    $body = [
        array_merge($esi_mock_return_data, [
            'id' => $character->character_id,
            'name' => $character->name,
            'category' => 'character'
        ])
    ];

    $data = json_encode($body);

    $response = new EsiResponse($data, [], 'now', 200);

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn($response);


    $expected_result = [
        'id' => $character->character_id,
        'character_id' => $character->character_id,
        'name' => $character->name,
        'corporation' => [
            'name' => $corporation->name
        ],
        'alliance' => [
            'name' => $alliance->name
        ]
    ];

    $service = new GetEntityFromId($character->character_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});
