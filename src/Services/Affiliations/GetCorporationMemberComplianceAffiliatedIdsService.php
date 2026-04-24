<?php

namespace Seatplus\Web\Services\Affiliations;

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Services\GetAffiliatedIds;

class GetCorporationMemberComplianceAffiliatedIdsService
{
    public function __construct(
        private readonly GetAffiliatedIds $getAffiliatedIds
    ) {}

    public static function make(): self
    {
        return new self(new GetAffiliatedIds(auth()->user()));
    }

    public function getQuery(): Builder
    {
        $affiliated_ids = $this->getAffiliatedIds->get(
            permissions: 'member compliance: review user',
            corporationRoles: 'director'
        );

        // Find users who have at least one character in the affiliated IDs
        // AND whose characters are in corporations/alliances with SSO scopes configured.
        $user_ids = User::query()
            ->whereHas(
                'characters',
                fn (Builder $query) => $query
                    ->where(fn (Builder $q) => $q
                        ->whereHas('corporation', fn (Builder $q) => $q->whereHas('ssoScopes', fn (Builder $q) => $q->whereIn('type', ['global', 'user'])))
                        ->orWhereHas('alliance', fn (Builder $q) => $q->whereHas('ssoScopes', fn (Builder $q) => $q->whereIn('type', ['global', 'user'])))
                    )
                    ->whereIn('character_infos.character_id', $affiliated_ids)
            )
            ->pluck('users.id')
            ->toArray();

        return CharacterUser::query()
            ->whereIn('user_id', $user_ids)
            ->select('character_id as affiliated_id');
    }
}
