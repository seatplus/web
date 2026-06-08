<?php

use Seatplus\EsiClient\EsiClient;
use Seatplus\Web\Services\GetCorporationInfo;

it('resolves corporation info via esi', function () {
    $corporation = test()->test_character->corporation;

    $esi = Mockery::mock(EsiClient::class);
    mockEsiTransport($esi, makeEsiResult((object) $corporation->toArray()));

    $result = (new GetCorporationInfo)->execute($esi, $corporation->corporation_id);

    expect($result->corporation_id)->toEqual($corporation->corporation_id);
    expect($result->name)->toEqual($corporation->name);
});
