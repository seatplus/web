<?php


namespace Seatplus\Web\Http\Controllers\Character;


use Seatplus\Eveapi\Jobs\Hydrate\Character\ContractHydrateBatch;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ContractRessource;

class ContractsController extends Controller
{
    public function index()
    {
        $ids = $this->getAffiliatedIds(Contract::class);

        $characters = CharacterAffiliation::whereIn('character_id', $ids)->with('character.corporation', 'character.alliance')->get();

        return inertia('Character/Contract/Index', [
            'dispatch_transfer_object' => $this->buildDispatchTransferObject(),
            'characters' => $characters,
        ]);
    }

    public function getCharacterContractsDetails(int $character_id)
    {
        $query = Contract::whereHas('characters', fn($query) => $query->whereCharacterId($character_id))
            ->with('items', 'start_location', 'end_location', 'assignee_character', 'assignee_corporation' ,'issuer_character', 'issuer_corporation');

        return ContractRessource::collection($query->paginate());
    }

    public function getContractDetails(int $character_id, int $contract_id)
    {

        $query = Contract::whereHas('characters', fn($query) => $query->whereCharacterId($character_id))
            ->whereContractId($contract_id)
            ->with('items', 'items.type', 'start_location', 'end_location', 'assignee_character', 'assignee_corporation' ,'issuer_character', 'issuer_corporation');

        return inertia('Character/Contract/ContractDetails', [
            'contract' => $query->first(),
        ]);
    }

    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(ContractHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . Contract::class),
            'required_scopes' => config('eveapi.scopes.character.contracts'),
            'required_corporation_role' => '',
        ];
    }
}