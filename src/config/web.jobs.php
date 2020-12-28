<?php

use Seatplus\Eveapi\Jobs\Hydrate\Character\CharacterAssetsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationMemberTrackingHydrateBatch;

return [
    'contacts' => ContactHydrateBatch::class,
    'membertracking' => CorporationMemberTrackingHydrateBatch::class,
    'assets' => CharacterAssetsHydrateBatch::class
];
