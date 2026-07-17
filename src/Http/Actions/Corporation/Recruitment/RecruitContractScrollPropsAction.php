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

namespace Seatplus\Web\Http\Actions\Corporation\Recruitment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Web\Http\Resources\ContractRessource;
use Seatplus\Web\Services\Query\LocationWatchListScope;
use Seatplus\Web\Services\Query\TypeWatchListScope;

/**
 * Builds the page scroll props the recruitment/review contract tab renders through native
 * Inertia <InfiniteScroll>, replacing the legacy axios character.contracts.details endpoint.
 * For each character it emits both the full list (contracts_<id>) and the watchlist-filtered
 * list (watchlisted_contracts_<id>), each with its own pageName so their scroll state never
 * collides.
 */
class RecruitContractScrollPropsAction
{
    /**
     * @param  array<int, int>  $characterIds
     * @param  array<string, mixed>  $watchlist
     * @return array<string, mixed>
     */
    public function execute(array $characterIds, array $watchlist): array
    {
        return Collection::make($characterIds)
            ->mapWithKeys(fn (int $characterId): array => [
                "contracts_{$characterId}" => Inertia::scroll(
                    fn () => $this->query($characterId)
                        ->paginate(pageName: "contracts_{$characterId}")
                        ->through(fn (Contract $contract) => (new ContractRessource($contract))->resolve()),
                ),
                "watchlisted_contracts_{$characterId}" => Inertia::scroll(
                    fn () => $this->query($characterId)
                        ->tap(new LocationWatchListScope($watchlist))
                        ->tap(new TypeWatchListScope($watchlist))
                        ->paginate(pageName: "watchlisted_contracts_{$characterId}")
                        ->through(fn (Contract $contract) => (new ContractRessource($contract))->resolve()),
                ),
            ])
            ->all();
    }

    /**
     * Base query for a character's contracts with everything the row cells render (mirrors
     * ContractsController::contractsQuery).
     *
     * @return Builder<Contract>
     */
    private function query(int $characterId): Builder
    {
        return Contract::whereHas('characters', fn (Builder $query) => $query->where('character_id', $characterId))
            ->with(['items', 'items.type', 'items.type.group', 'startLocation.locatable', 'endLocation.locatable', 'assigneeCharacter', 'assigneeCorporation', 'issuerCharacter', 'issuerCorporation']);
    }
}
