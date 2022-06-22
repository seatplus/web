<?php

namespace Seatplus\Web\Http\Pipelines;

use Seatplus\Auth\DataTransferObjects\CheckPermissionAffiliationDto;
use Seatplus\Auth\Pipelines\Middleware\CheckPermissionAffiliationPipeline;
use Seatplus\Web\Services\GetRecruitIdsService;

class CheckRecruitsAffiliatedIdPipe extends CheckPermissionAffiliationPipeline
{

    protected function check(CheckPermissionAffiliationDto $checkPermissionAffiliationDto): CheckPermissionAffiliationDto
    {
        $validated_ids = GetRecruitIdsService::get();

        $checkPermissionAffiliationDto->mergeValidatedIds($validated_ids);

        return $checkPermissionAffiliationDto;
    }

    protected function shouldBeChecked(CheckPermissionAffiliationDto $checkPermissionAffiliationDto): bool
    {
        if ($checkPermissionAffiliationDto->allIdsValidated()) {
            return false;
        }

        if ($checkPermissionAffiliationDto->requested_ids->count() > 1) {
            return false;
        }

        return true;
    }
}