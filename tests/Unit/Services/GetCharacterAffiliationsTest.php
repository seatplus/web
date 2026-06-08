<?php

use Seatplus\EsiClient\EsiClient;
use Seatplus\Web\Services\GetCharacterAffiliations;

it('resolves character affiliations via esi', function () {
    $affiliation = (object) [
        'alliance_id' => 123,
        'character_id' => 456,
        'corporation_id' => 789,
        'faction_id' => null,
    ];

    $esi = Mockery::mock(EsiClient::class);
    mockEsiTransport($esi, makeEsiResult([$affiliation]));

    $result = (new GetCharacterAffiliations)->execute($esi, [456]);

    expect($result)->toHaveCount(1);
    expect($result->first()->character_id)->toEqual(456);
    expect($result->first()->corporation_id)->toEqual(789);
});
