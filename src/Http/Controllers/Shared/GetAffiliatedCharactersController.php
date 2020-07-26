<?php


namespace Seatplus\Web\Http\Controllers\Shared;


use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\CharacterInfoRessource;

class GetAffiliatedCharactersController extends Controller
{
    public function __invoke(string $permission)
    {
        $query = CharacterInfo::whereIn('character_id', auth()->user()->getAffiliatedCharacterIdsByPermission($permission));

        return CharacterInfoRessource::collection($query->paginate());
    }

}
