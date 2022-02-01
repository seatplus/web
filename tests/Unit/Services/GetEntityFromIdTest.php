<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use Seatplus\EsiClient\DataTransferObjects\EsiResponse;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Seatplus\Web\Services\GetEntityFromId;

test('happy path', function () {
    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $expected_result = [
        'id'           => $character_affiliation->character_id,
        'character_id' => $character_affiliation->character_id,
        'name'         => $character_affiliation->character->name,
        'corporation'  => [
            'name' => $character_affiliation->corporation->name,
        ],
        'alliance' => [
            'name' => $character_affiliation->alliance->name,
        ],
    ];

    $cache_key = sprintf('entityById:%s', $character_affiliation->character_id);
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
        'id'           => $character_affiliation->character_id,
        'character_id' => $character_affiliation->character_id,
        'name'         => $character_affiliation->character->name,
        'corporation'  => [
            'name' => $character_affiliation->corporation->name,
        ],
    ];

    $service = new GetEntityFromId($character_affiliation->character_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});

test('happy path via corporation id', function () {
    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $expected_result = [
        'id'             => $character_affiliation->corporation_id,
        'corporation_id' => $character_affiliation->corporation_id,
        'name'           => $character_affiliation->corporation->name,
        'alliance'       => [
            'name' => $character_affiliation->alliance->name,
        ],
    ];

    $service = new GetEntityFromId($character_affiliation->corporation_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});

test('happy path via alliance id', function () {
    $character = CharacterInfo::factory()->create();

    $character_affiliation = $character->character_affiliation;

    $expected_result = [
        'id'          => $character_affiliation->alliance_id,
        'alliance_id' => $character_affiliation->alliance_id,
        'name'        => $character_affiliation->alliance->name,
    ];

    $service = new GetEntityFromId($character_affiliation->alliance_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});

test('unknown character id', function () {
    $alliance = AllianceInfo::factory()->create([
        'alliance_id' => 99000000123,
    ]);

    $corporation = CorporationInfo::factory()->create([
        'alliance_id' => $alliance->alliance_id,
    ]);
    $character = CharacterInfo::factory()->make([
        'corporation_id' => $corporation->corporation_id,
    ]);

    $esi_mock_return_data = [
        'alliance_id'    => $alliance->alliance_id,
        'character_id'   => $character->character_id,
        'corporation_id' => $corporation->corporation_id,
        'faction_id'     => null,
    ];

    $body = [
        array_merge($esi_mock_return_data, [
            'id'       => $character->character_id,
            'name'     => $character->name,
            'category' => 'character',
        ]),
    ];

    $data = json_encode($body);

    $response = new EsiResponse($data, [], 'now', 200);

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn($response);

    $expected_result = [
        'id'           => $character->character_id,
        'character_id' => $character->character_id,
        'name'         => $character->name,
        'corporation'  => [
            'name' => $corporation->name,
        ],
        'alliance' => [
            'name' => $alliance->name,
        ],
    ];

    $service = new GetEntityFromId($character->character_id);

    $result = $service->execute();

    expect($result)->toEqual($expected_result);
});
