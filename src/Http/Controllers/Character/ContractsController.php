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

use Illuminate\Http\Request;
use Seatplus\Auth\Services\CharacterAffiliations\GetOwnedCharacterAffiliationsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContractRessource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Traits\HasWatchlist;

class ContractsController extends Controller
{
    use HasWatchlist;

    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->create(Contract::class);

        $affiliations_dto = new AffiliationsDto(
            permission: data_get($dispatchTransferObject, 'permission'),
            user: auth()->user()
        );

        $owned_characters = GetOwnedCharacterAffiliationsService::make($affiliations_dto)
            ->getQuery();

        $characters = CharacterAffiliation::query()
            ->when(
                request()->has('character_ids'),
                fn ($query) => $query->whereIn('character_id', request()->get('character_ids')),
                fn ($query) => $query->joinSub($owned_characters, 'owned_characters', 'character_affiliations.character_id', '=', 'owned_characters.character_id')
            )
            ->has('character.contracts')
            ->with('character.corporation', 'character.alliance')->get();

        return inertia('Character/Contract/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characters' => $characters,
        ]);
    }

    public function getCharacterContractsDetails(int $character_id, Request $request)
    {
        $query = Contract::whereHas('characters', fn ($query) => $query->whereCharacterId($character_id))
            ->with(['items', 'items.type', 'items.type.group', 'start_location', 'end_location', 'assignee_character', 'assignee_corporation', 'issuer_character', 'issuer_corporation']);

        $this->handleWatchlist($query, $request);

        return ContractRessource::collection($query->paginate());
    }

    public function getContractDetails(int $character_id, int $contract_id)
    {
        $query = Contract::query()->whereHas('characters', fn ($query) => $query->whereCharacterId($character_id))
            ->whereContractId($contract_id)
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
