<?php

use Seatplus\EsiClient\EsiClient;
use Seatplus\Web\Services\GetNamesFromIdsService;

it('resolves ids to names via esi and caches them', function () {
    $id = test()->test_character->character_id;

    $esi = Mockery::mock(EsiClient::class);
    mockEsiTransport($esi, makeEsiResult([
        (object) [
            'id' => $id,
            'name' => test()->test_character->name,
            'category' => 'character',
        ],
    ]));

    $cacheKey = sprintf('name:%s', $id);

    // has() rather than expecting cache($cacheKey) to be null: the latter narrows that
    // expression to null for PHPStan, and the read below is then an impossible access.
    expect(cache()->has($cacheKey))->toBeFalse();

    $result = (new GetNamesFromIdsService)->execute($esi, [$id]);

    expect($result)->toHaveCount(1);
    expect($result->first()->name)->toEqual(test()->test_character->name);
    expect(cache($cacheKey)->name)->toEqual(test()->test_character->name);
});

it('returns cached names without calling esi', function () {
    $id = test()->test_character->character_id;

    $cached = (object) [
        'id' => $id,
        'name' => test()->test_character->name,
        'category' => 'character',
    ];

    cache([sprintf('name:%s', $id) => $cached], now()->addMinute());

    $esi = Mockery::mock(EsiClient::class);
    $esi->shouldReceive('invoke')->never();

    $result = (new GetNamesFromIdsService)->execute($esi, [$id]);

    expect($result->first()->name)->toEqual(test()->test_character->name);
});
