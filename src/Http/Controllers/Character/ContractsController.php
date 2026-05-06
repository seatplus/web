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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Response;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContractRessource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Query\LocationWatchListScope;
use Seatplus\Web\Services\Query\TypeWatchListScope;

class ContractsController extends Controller
{
    public function index(Request $request): Response
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->create(Contract::class);

        $character_ids = $request->get('character_ids') ?? $this->getOwnedCharacterIds();

        $characters = CharacterAffiliation::query()
            ->whereIn('character_id', $character_ids)
            ->has('character.contracts')
            ->with(['character.corporation', 'character.alliance'])
            ->get();

        return inertia('Character/Contract/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characters' => $characters,
        ]);
    }

    public function getCharacterContractsDetails(int $character_id, Request $request): AnonymousResourceCollection
    {
        $query = Contract::whereHas('characters', fn (Builder $query) => $query->where('character_id', $character_id))
            ->with(['items', 'items.type', 'items.type.group', 'start_location', 'end_location', 'assignee_character', 'assignee_corporation', 'issuer_character', 'issuer_corporation'])
            ->tap(new LocationWatchListScope($request->all()))
            ->tap(new TypeWatchListScope($request->all()));

        return ContractRessource::collection($query->paginate());
    }

    public function getContractDetails(int $character_id, int $contract_id): string|Response
    {
        $query = Contract::query()->whereHas('characters', fn (Builder $query) => $query->where('character_id', $character_id))
            ->where('contract_id', $contract_id)
            ->with('items', 'items.type', 'start_location', 'end_location', 'assignee_character', 'assignee_corporation', 'issuer_character', 'issuer_corporation');

        if (request()->header('X-Modal', false)) {
            return $query->get()->toJson();
        }

        return inertia('Character/Contract/ContractDetails', [
            'contract' => $query->first(),
            'activeSidebarElement' => 'character.contracts',
        ]);
    }
}
