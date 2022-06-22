<?php

namespace Seatplus\Web\Http\Pipelines;

use Seatplus\Auth\DataTransferObjects\CheckPermissionAffiliationDto;
use Seatplus\Auth\Pipelines\Middleware\CheckPermissionAffiliationPipeline;
use Seatplus\Auth\Services\Affiliations\GetAffiliatedIdsService;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Web\Services\GetRecruitIdsService;

class CheckCharacterAffiliationsAffiliatedIdPipe extends CheckPermissionAffiliationPipeline
{

    protected function check(CheckPermissionAffiliationDto $checkPermissionAffiliationDto): CheckPermissionAffiliationDto
    {
        $owned_ids = GetOwnedAffiliatedIdsService::make($checkPermissionAffiliationDto->affiliationsDto)->getQuery();
        $affiliated_ids = GetAffiliatedIdsService::make($checkPermissionAffiliationDto->affiliationsDto)->getQuery();

        $recruits = CharacterAffiliation::query()
            ->whereIn('character_id', GetRecruitIdsService::get())
            ->select(['character_id', 'corporation_id', 'alliance_id']);

        $validated_ids = CharacterAffiliation::query()
            ->joinSub(
                $owned_ids->union($affiliated_ids),
                'affiliated',
                'affiliated.affiliated_id',
                '=',
                'character_affiliations.character_id'
            )
            ->union($recruits)
            ->select(['character_id', 'corporation_id', 'alliance_id'])
            ->get()
            ->map(fn(CharacterAffiliation $characterAffiliation) => $characterAffiliation->getAttributes())
            ->flatten()
            ->filter()
            ->unique()
            ->values();

        $checkPermissionAffiliationDto->mergeValidatedIds($validated_ids);

        return $checkPermissionAffiliationDto;
    }

    protected function shouldBeChecked(CheckPermissionAffiliationDto $checkPermissionAffiliationDto): bool
    {
        if ($checkPermissionAffiliationDto->allIdsValidated()) {
            return false;
        }

        return true;
    }
}