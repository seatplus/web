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
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContractRessource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

class ContractsController extends Controller
{
    public function index(Request $request): Response
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->create(Contract::class);

        // Default view = own characters; a submitted character_ids selection is honoured only within the
        // authorised set (own ∪ affiliated). This route has no CheckAuthorization middleware, so the
        // query is the sole guard — previously a submitted character_ids was used verbatim (tamper).
        // Mirrors ContactsController: CharacterInfo scoped by constrainToSelectionOrOwned, filtered to
        // those with contracts. The view only reads character_id (the scroll prop carries the rows).
        $charactersQuery = CharacterInfo::query()->has('contracts');

        $this->getAffiliatedIds->constrainToSelectionOrOwned(
            query: $charactersQuery,
            column: 'character_id',
            selectedIds: $request->get('character_ids'),
            permissions: $dispatchTransferObject->permission,
            corporationRoles: $dispatchTransferObject->required_corporation_role,
        );

        $characters = $charactersQuery->get();

        // One InfiniteScroll prop per character (mirrors the wallet migration). Each
        // paginator carries the ContractRessource shape and its own pageName so the
        // per-character scroll state never collides.
        $contracts = $characters->mapWithKeys(fn (CharacterInfo $character): array => [
            "contracts_{$character->character_id}" => Inertia::scroll(
                fn () => $this->contractsQuery($character->character_id)
                    ->paginate(pageName: "contracts_{$character->character_id}")
                    ->through(fn (Contract $contract) => (new ContractRessource($contract))->resolve()),
            ),
        ])->all();

        return inertia('Character/Contract/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characters' => $characters,
            ...$contracts,
        ]);
    }

    /**
     * Base query for a character's contracts with everything the row cells render.
     * The watchlist scopes are applied only on the recruitment endpoint below.
     *
     * @return Builder<Contract>
     */
    private function contractsQuery(int $character_id): Builder
    {
        return Contract::whereHas('characters', fn (Builder $query) => $query->where('character_id', $character_id))
            ->with(['items', 'items.type', 'items.type.group', 'startLocation.locatable', 'endLocation.locatable', 'assigneeCharacter', 'assigneeCorporation', 'issuerCharacter', 'issuerCorporation']);
    }

    public function getContractDetails(int $character_id, int $contract_id): string|Response
    {
        // Authorisation (incl. the recruiter → recruit extended-scope case) is handled by the
        // CheckAuthorizationWithExtendedScope middleware on the contract.details route.
        $query = Contract::query()->whereHas('characters', fn (Builder $query) => $query->where('character_id', $character_id))
            ->where('contract_id', $contract_id)
            ->with('items', 'items.type', 'startLocation', 'endLocation', 'assigneeCharacter', 'assigneeCorporation', 'issuerCharacter', 'issuerCorporation');

        if (request()->header('X-Modal', false)) {
            return $query->get()->toJson();
        }

        return inertia('Character/Contract/ContractDetails', [
            'contract' => $query->first(),
            'activeSidebarElement' => 'character.contracts',
        ]);
    }
}
