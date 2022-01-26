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

namespace Seatplus\Web\Http\Controllers\Character;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\Balance;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\GetAffiliatedIdsService;

class WalletsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()->create(WalletJournal::class);

        $ids = GetAffiliatedIdsService::make()
            ->viaDispatchTransferObject($dispatchTransferObject)
            ->setRequestFlavour('character')
            ->get();

        return inertia('Character/Wallet/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'character_ids' => $ids,
        ]);
    }

    public function journal(int $character_id)
    {
        $query =  WalletJournal::query()
            ->where('wallet_journable_id', $character_id)
            ->with('wallet_journable')
            ->orderByDesc('date');

        request()->whenHas('ref_type', fn(array $types) => $query->whereIn('ref_type', $types));

        return $query->paginate();
    }

    public function journalTypes()
    {
        $term = request()->get('search');

        if (Str::length($term) < 3) {
            return response('the minimum length of 3 is not met', 403);
        }

        $alphabet_to_number = function ($string) : float {
            $string = strtoupper($string);
            $length = strlen($string);
            $number = 0;
            $level = 1;
            while ($length >= $level ) {
                $char = $string[$length - $level];
                $c = ord($char) - 64;
                $number += $c * (26 ** ($level-1));
                $level++;
            }
            return $number;
        };

        return WalletJournal::query()
            ->where('ref_type', 'like', $term . '%')
            ->select('ref_type')
            ->groupBy('ref_type')
            ->limit(15)
            ->get()
            ->map(fn ($group, $index) => [
                'id' => $alphabet_to_number($group->ref_type),
                'name' => $group->ref_type,
                'hasEveImage' => false
            ]);
    }

    public function balance(int $character_id)
    {
        $balance_part = Balance::query()
            ->whereHasMorph(
                'balanceable',
                CharacterInfo::class,
                fn (Builder $query) => $query->where('character_id', $character_id)
            )
            ->limit(1)
            ->select(['updated_at as x', 'balance as y']);

        $date_part = WalletJournal::query()
            ->whereBetween('date', [now(), now()->subDays(request()->get('days', 30))])
            ->orderByDesc('date')
            ->select(['date as x', 'balance as y']);

        return WalletJournal::query()
            ->limit(90)
            ->orderByDesc('date')
            ->union($date_part)
            ->union($balance_part)
            ->where('wallet_journable_id', $character_id)
            ->select(['date as x', 'balance as y'])
            ->paginate();
    }

    public function transaction(int $character_id)
    {
        return WalletTransaction::where('wallet_transactionable_id', $character_id)
            ->with('type', 'location')
            ->orderByDesc('date')
            ->paginate();
    }
}
