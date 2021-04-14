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

namespace Seatplus\Web\Http\Controllers\Corporation\Wallet;

use Seatplus\Eveapi\Models\Corporation\CorporationDivision;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\GetAffiliatedIdsService;

class CorporationWalletController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->setIsCharacter(false)
            ->create(WalletJournal::class);

        return inertia('Corporation/Wallets/Wallet', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'corporationDivisions' => $this->getAffiliatedCorporateWalletDivisions($dispatchTransferObject),
        ]);
    }

    public function journal(int $corporation_id, int $division_id)
    {
        return WalletJournal::where('wallet_journable_id', $corporation_id)
            ->where('division', $division_id)
            ->with('wallet_journable')
            ->orderByDesc('date')
            ->paginate();
    }

    public function balance(int $corporation_id, int $division_id)
    {
        $date_part = WalletJournal::query()
            ->whereBetween('date', [now(), now()->subDays(request()->get('days', 30))])
            ->orderByDesc('date')
            ->select(['date as x', 'balance as y']);

        return WalletJournal::query()
            ->limit(90)
            ->orderByDesc('date')
            ->union($date_part)
            ->where('wallet_journable_id', $corporation_id)
            ->where('division', $division_id)
            ->select(['date as x', 'balance as y'])
            ->paginate();
    }

    public function transaction(int $corporation_id, int $division_id)
    {
        return WalletTransaction::where('wallet_transactionable_id', $corporation_id)
            ->where('division', $division_id)
            ->with('type', 'location')
            ->orderByDesc('date')
            ->paginate();
    }

    private function getAffiliatedCorporateWalletDivisions(object $dispatchTransferObject)
    {
        $ids = GetAffiliatedIdsService::make()
            ->viaDispatchTransferObject($dispatchTransferObject)
            ->setRequestFlavour('corporation')
            ->get();

        return CorporationDivision::whereIn('corporation_id', $ids)
            ->where('division_type', 'wallet')
            ->get();
    }
}
