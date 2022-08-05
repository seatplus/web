<?php

namespace Seatplus\Web\Services\Affiliations;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Affiliations\GetAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;

class GetCorporationMemberComplianceAffiliatedIdsService
{

    public function __construct(
        private AffiliationsDto $affiliationsDto
    ) {
    }

    public static function make()
    {

        $affiliationsDto = new AffiliationsDto(
            user: auth()->user(),
            permissions: ['member compliance: review user'],
            corporation_roles: ['director'],
        );

        return new static($affiliationsDto);
    }

    public function getQuery() : Builder
    {

        $affiliated_ids = GetAffiliatedIdsService::make($this->affiliationsDto)->getQuery();

        $user_query = User::query()
            ->whereHas(
                'characters',
                fn (Builder $query) => $query
                    ->whereHas(
                        'corporation',
                        fn (Builder $query) => $query->whereHas('ssoScopes', fn (Builder $query) => $query->whereIn('type', ['global', 'user']))
                    )
                    ->orWhereHas(
                        'alliance',
                        fn (Builder $query) => $query->whereHas('ssoScopes', fn (Builder $query) => $query->whereIn('type', ['global', 'user']))
                    )
                    ->joinSub(
                        $affiliated_ids,
                        'affiliated',
                        'character_infos.character_id',
                        '=',
                        'affiliated.affiliated_id'
                    )
            )
            ->select('users.id');

        $character_user_query = CharacterUser::query()
            ->joinSub($user_query, 'users', 'id', 'user_id')
            ->select('character_id as affiliated_id');

        return $character_user_query
            ->union($affiliated_ids);

    }
}