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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\Balance;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Actions\Wallet\GetRefTypesAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

class WalletsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()->create(WalletJournal::class);

        $ids = $this->getCharacterIds($dispatchTransferObject, 'wallet_journals');

        return inertia('Character/Wallet/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'character_ids' => $ids,
        ]);
    }

    public function journal(int $character_id)
    {
        $query = WalletJournal::query()
            ->where('wallet_journable_id', $character_id)
            ->with('wallet_journable')
            ->orderByDesc('date');

        request()->whenHas('ref_type', fn (array $types) => $query->whereIn('ref_type', $types));

        return $query->paginate();
    }

    public function journalTypes(GetRefTypesAction $action)
    {
        $term = request()->get('search');

        if (Str::length($term) < 3) {
            return response('the minimum length of 3 is not met', 403);
        }

        return $action->execute($term);
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
            ->select(DB::raw('DATE(updated_at) as x'), 'balance as y');

        $journal_entries = WalletJournal::query()
            ->select(DB::raw('DATE(date) as x'), DB::raw('AVG(balance) as y'))
            ->orderByDesc('date')
            ->where('wallet_journable_id', $character_id)
            ->groupBy('x')
            ->limit(30);

        return new LengthAwarePaginator($balance_part->union($journal_entries)->limit(30)->get(), 30, 30);
    }

    public function transaction(int $character_id)
    {
        return WalletTransaction::where('wallet_transactionable_id', $character_id)
            ->with('type', 'location')
            ->orderByDesc('date')
            ->paginate();
    }
}
