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

use Seatplus\Eveapi\Jobs\Hydrate\Character\CharacterAssetsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContractHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\SkillsHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Character\WalletHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationMemberTrackingHydrateBatch;
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationWalletHydrateBatch;

return [
    'contacts' => ContactHydrateBatch::class,
    'membertracking' => CorporationMemberTrackingHydrateBatch::class,
    'assets' => CharacterAssetsHydrateBatch::class,
    'wallet' => WalletHydrateBatch::class,
    'contract' => ContractHydrateBatch::class,
    'corporation.wallet' => CorporationWalletHydrateBatch::class,
    'skills' => SkillsHydrateBatch::class
];
