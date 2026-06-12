<?php

use Seatplus\EsiClient\EsiClient;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Web\Services\SearchService;

it('searches esi with a scoped token and returns the raw result', function () {
    $token = test()->test_character->refresh_token;
    updateRefreshTokenWithScopes($token, ['esi-search.search_structures.v1']);

    $system = System::factory()->create(['name' => 'jita']);

    $esi = Mockery::mock(EsiClient::class);
    mockEsiTransport($esi, makeEsiResult((object) [
        'solar_system' => [$system->system_id],
    ]));

    $result = (new SearchService)->execute($esi, $token, ['solar_system'], 'jit');

    expect($result->solar_system)->toEqual([$system->system_id]);
});
