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

namespace Seatplus\Web\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\Controller\DispatchTransferObject;
use Seatplus\Web\Services\GetAffiliatedIds;

class Controller extends BaseController
{
    use ValidatesRequests;

    private const string CHARACTER_IDS_FILTER = 'character_ids';

    public function __construct(
        protected readonly Request $request,
        protected readonly GetAffiliatedIds $getAffiliatedIds
    ) {}

    protected function getCharacterIds(
        DispatchTransferObject $dispatchTransferObject,
        ?string $characterRelation = null
    ): Collection {
        // The frontend iterates this as a list of scalar character_ids
        // (`:id="character_id"`, typed Number). Returning full CharacterInfo models
        // made each element an object, so pages built route('…', ['character_id' =>
        // <object>]) and Ziggy threw "object passed as 'character_id' parameter …",
        // crashing the setup() of components that resolve a route on mount (e.g.
        // WalletJournalBalanceChart) and taking the page down. Pluck plain ids.
        //
        // Default view = the user's own characters. An explicit character_ids selection is honoured
        // only *within* the authorised set (own ∪ affiliated, composed as a subquery): the affiliation
        // scope() and the requested whereIn() are ANDed, so a submitted id the user isn't affiliated
        // with is dropped — a tampered URL can't reach out-of-scope characters (this route carries no
        // CheckAuthorization middleware, so the query is the sole tamper guard).
        $query = CharacterInfo::query()->select('character_id');

        $this->getAffiliatedIds->constrainToSelectionOrOwned(
            query: $query,
            column: 'character_id',
            selectedIds: $this->request->get(self::CHARACTER_IDS_FILTER),
            permissions: $dispatchTransferObject->permission,
            corporationRoles: $dispatchTransferObject->required_corporation_role,
        );

        return $query
            ->when(
                $characterRelation,
                fn (Builder $query) => $query->with($characterRelation),
            )
            ->pluck('character_id');
    }

    protected function getAffiliatedIds(DispatchTransferObject $dispatchTransferObject): array
    {
        return $this->getAffiliatedIds->get(
            $dispatchTransferObject->permission,
            $dispatchTransferObject->required_corporation_role
        );
    }

    protected function getOwnedCharacterIds(): array
    {
        return auth()->user()->characters->pluck('character_id')->toArray();
    }
}
