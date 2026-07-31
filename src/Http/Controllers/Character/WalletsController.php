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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Wallet\Balance;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Eveapi\Models\Wallet\WalletTransaction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Support\Translations;

class WalletsController extends Controller
{
    public function index(): Response
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()->create(WalletJournal::class);

        $characterIdQuery = CharacterInfo::query()->select('character_id');
        $this->getAffiliatedIds->constrainToSelectionOrOwned(
            query: $characterIdQuery,
            column: 'character_id',
            selectedIds: request('character_ids'),
            permissions: $dispatchTransferObject->permission,
            corporationRoles: $dispatchTransferObject->required_corporation_role,
        );
        $ids = $characterIdQuery->pluck('character_id');

        // One infinite-scroll prop per character — the page renders a wallet card
        // per id. Each paginator uses its own pageName so their scroll state never
        // collides; the <InfiniteScroll> component reads it via the matching key.
        $journals = $ids->mapWithKeys(fn (int $character_id) => [
            "journal_{$character_id}" => Inertia::scroll(
                fn () => $this->journalQuery($character_id)->paginate(pageName: "journal_{$character_id}"),
            ),
        ])->all();

        // Transactions scroll the same way as journals (one prop + pageName per character).
        $transactions = $ids->mapWithKeys(fn (int $character_id) => [
            "transaction_{$character_id}" => Inertia::scroll(
                fn () => $this->transactionQuery($character_id)->paginate(pageName: "transaction_{$character_id}"),
            ),
        ])->all();

        // Balance chart data — deferred (loads after the initial render) so it never
        // blocks the page, replacing the old client-side axios fetch.
        $balances = $ids->mapWithKeys(fn (int $character_id) => [
            "balance_{$character_id}" => Inertia::defer(fn () => $this->balanceData($character_id)),
        ])->all();

        return inertia('Character/Wallet/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'character_ids' => $ids,
            'pageTranslations' => Translations::gather(['web::wallet_journal']),
            // ref_type filter options, resolved from the accessible journals so the
            // filter is a plain prop-fed select (no autosuggest endpoint / Ziggy).
            'ref_types' => WalletJournal::query()
                ->whereIn('wallet_journable_id', $ids)
                ->distinct()
                ->orderBy('ref_type')
                ->pluck('ref_type'),
            ...$journals,
            ...$transactions,
            ...$balances,
        ]);
    }

    private function journalQuery(int $character_id): Builder
    {
        $query = WalletJournal::query()
            ->where('wallet_journable_id', $character_id)
            ->with('walletJournable')
            ->orderByDesc('date');

        // whenFilled (not whenHas): an empty ref_type filter means "no filter",
        // not whereIn([]) which would return nothing.
        request()->whenFilled('ref_type', fn (array $types) => $query->whereIn('ref_type', $types));

        return $query;
    }

    private function balanceData(int $character_id): Collection
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
            ->orderByDesc('x')
            ->where('wallet_journable_id', $character_id)
            ->groupBy('x')
            ->limit(30);

        return $balance_part->union($journal_entries)->limit(30)->get();
    }

    private function transactionQuery(int $character_id): Builder
    {
        return WalletTransaction::where('wallet_transactionable_id', $character_id)
            ->with('type', 'location')
            ->orderByDesc('date');
    }
}
