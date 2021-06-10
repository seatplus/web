<?php


namespace Seatplus\Web\Http\Controllers\Character;


use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Skills\SkillQueue;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\GetAffiliatedIdsService;

class SkillsController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()->create(Skill::class);

        $ids = GetAffiliatedIdsService::make()
            ->viaDispatchTransferObject($dispatchTransferObject)
            ->setRequestFlavour('character')
            ->get();

        return inertia('Character/Skill/Index', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'character_ids' => $ids,
        ]);

    }

    public function skills(int $character_id)
    {
        return Skill::query()
            ->with('type.group')
            ->where('character_id', $character_id)
            ->paginate();
    }

    public function skillQueue(int $character_id)
    {
        return SkillQueue::query()
            ->with('type.group')
            ->where('character_id', $character_id)
            ->paginate();
    }

}