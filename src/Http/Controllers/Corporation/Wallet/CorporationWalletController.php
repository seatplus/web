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

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Corporation\CorporationDivision;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

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
        $entries = WalletJournal::query()
            ->select(DB::raw('DATE(date) as x'), DB::raw('AVG(balance) as y'))
            ->orderByDesc('date')
            ->where('wallet_journable_id', $corporation_id)
            ->where('division', $division_id)
            ->groupBy('x')
            ->limit(30)
            ->get();

        return new LengthAwarePaginator($entries, 30, 30);
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
        $affiliations_dto = new AffiliationsDto(
            permissions: [data_get($dispatchTransferObject, 'permission')],
            user: auth()->user(),
            corporation_roles: data_get($dispatchTransferObject, 'required_corporation_role')
        );

        $owned_corporations = GetOwnedAffiliatedIdsService::make($affiliations_dto)
            ->getQuery();

        //whereIn('corporation_id', $ids)
        return CorporationDivision::query()
            ->where('division_type', 'wallet')
            ->when(
                request()->has('corporation_ids'),
                fn ($query) => $query->whereIn('corporation_id', request()->get('corporation_ids')),
                fn ($query) => $query->joinSub($owned_corporations, 'owned_corporations', 'owned_corporations.affiliated_id', '=', 'corporation_divisions.corporation_id')
            )
            ->select('corporation_divisions.*')
            ->distinct()
            ->get();
    }
}
