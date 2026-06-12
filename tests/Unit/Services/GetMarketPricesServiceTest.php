<?php

use Seatplus\EsiClient\EsiClient;
use Seatplus\Web\Services\GetMarketPricesService;

it('fetches market prices via esi', function () {
    $esi = Mockery::mock(EsiClient::class);
    mockEsiTransport($esi, makeEsiResult([
        (object) ['adjusted_price' => 0, 'average_price' => 31_214_609.93, 'type_id' => 43691],
        (object) ['adjusted_price' => 1_005_248.13, 'average_price' => 1_002_393.46, 'type_id' => 32772],
        (object) ['adjusted_price' => 111_879.42, 'average_price' => 104_750.07, 'type_id' => 32774],
    ]));

    $result = (new GetMarketPricesService)->execute($esi);

    expect($result)->toHaveCount(3);
    expect($result->first()->type_id)->toEqual(43691);
});
