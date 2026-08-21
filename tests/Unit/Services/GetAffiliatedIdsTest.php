<?php

use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetAffiliatedIds;

it('fails closed when asked to constrain a non character column to affiliated except owned', function () {
    // The affiliated-except-owned primitive covers the character id-space only: corporation roles
    // grant corporation ids, which this method has no way to subtract owned characters from. Guessing
    // an id-space would silently widen a listing, so it throws instead.
    expect(fn () => (new GetAffiliatedIds(test()->test_user))->constrainToAffiliatedCharactersExceptOwned(
        query: CorporationInfo::query(),
        column: 'corporation_id',
        permissions: 'can open or close corporations recruitment',
    ))->toThrow(InvalidArgumentException::class);
});
