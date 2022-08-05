<?php

namespace Seatplus\Web\Http\Pipelines;

use Seatplus\Auth\DataTransferObjects\CheckPermissionAffiliationDto;
use Seatplus\Auth\Pipelines\Middleware\CheckPermissionAffiliationPipeline;
use Seatplus\Web\Services\Affiliations\GetCorporationMemberComplianceAffiliatedIdsService;

class CheckCorporationMemberComplianceAffiliatedIdPipe extends CheckPermissionAffiliationPipeline
{
    protected function check(CheckPermissionAffiliationDto $checkPermissionAffiliationDto): CheckPermissionAffiliationDto
    {
        $validated_ids = GetCorporationMemberComplianceAffiliatedIdsService::make()
            ->getQuery()
            ->pluck('affiliated_id')
            ->values();

        $checkPermissionAffiliationDto->mergeValidatedIds($validated_ids);

        return $checkPermissionAffiliationDto;
    }

    protected function shouldBeChecked(CheckPermissionAffiliationDto $checkPermissionAffiliationDto): bool
    {
        if ($checkPermissionAffiliationDto->allIdsValidated()) {
            return false;
        }

        if (! $checkPermissionAffiliationDto->affiliationsDto->user->can('member compliance: review user')) {
            return false;
        }

        return true;
    }
}
