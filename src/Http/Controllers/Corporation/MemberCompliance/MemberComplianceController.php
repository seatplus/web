<?php


namespace Seatplus\Web\Http\Controllers\Corporation\MemberCompliance;


use Illuminate\Database\Eloquent\Builder;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Resources\CharacterComplianceResource;
use Seatplus\Web\Http\Resources\UserComplianceResource;

class MemberComplianceController
{
    public function index()
    {
        $affiliated_ids = getAffiliatedIdsByPermission('view member compliance', 'director');

        $affiliated_corporations = CorporationInfo::whereIn('corporation_id', $affiliated_ids)
            ->has('ssoScopes')
            ->orHas('alliance.ssoScopes')
            ->select('name', 'corporation_id')
            ->get();

        return inertia('Corporation/MemberCompliance/MemberCompliance',[
            'corporations' => $affiliated_corporations
        ]);
    }

    public function getCharacterCompliance(int $corporation_id)
    {
        $members = CharacterInfo::whereHas('corporation', fn(Builder $query) => $query
            ->where('corporation_infos.corporation_id', $corporation_id)
            ->whereHas('ssoScopes', fn(Builder $query) => $query->whereType('default'))
        );

        return CharacterComplianceResource::collection($members->paginate());
    }

    public function getUserCompliance(int $corporation_id)
    {

        $users = $this->getUsersBuilder($corporation_id);

        return UserComplianceResource::collection($users->paginate());
    }

    public function getMissingCharacters(int $corporation_id)
    {
        $users = $this->getUsersBuilder($corporation_id);

        $known_ids = $users->cursor()
            ->map( fn($user) => $user->characters)
            ->map( fn($character) => $character->pluck('character_id'))
            ->flatten()
            ->unique()
            ->all();

        $members = CharacterInfo::whereHas('corporation', fn(Builder $query) => $query
            ->where('corporation_infos.corporation_id', $corporation_id)
            ->whereHas('ssoScopes', fn(Builder $query) => $query->whereType('user')))
            ->whereNotIn('character_id',$known_ids);

        return CharacterComplianceResource::collection($members->paginate());
    }

    private function getUsersBuilder(int $corporation_id) : Builder
    {
        return User::whereHas('characters.corporation', fn(Builder $query) => $query
            ->where('corporation_infos.corporation_id', $corporation_id)
            ->whereHas('ssoScopes', fn(Builder $query) => $query->whereType('user'))
        );
    }
}
