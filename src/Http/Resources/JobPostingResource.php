<?php

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Support\CorporationShape;

/**
 * @mixin Enlistment
 */
class JobPostingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CorporationInfo $corporation */
        $corporation = $this->corporation;

        return [
            'corporation_id' => $this->corporation_id,
            // 'user' = the whole account applies; 'character' = a single character applies.
            'type' => $this->type,
            'corporation' => CorporationShape::make($corporation),
            'stages' => $this->reviewRounds
                ->map(fn (EnlistmentReviewRound $round) => [
                    'position' => $round->position,
                    'label' => $round->label,
                ])
                ->values()
                ->all(),
        ];
    }
}
