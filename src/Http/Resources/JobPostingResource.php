<?php

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

/**
 * @mixin Enlistment
 */
class JobPostingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'corporation_id' => $this->corporation_id,
            // 'user' = the whole account applies; 'character' = a single character applies.
            'type' => $this->type,
            'corporation' => [
                'corporation_id' => $this->corporation->corporation_id,
                'name' => $this->corporation->name,
                'ticker' => $this->corporation->ticker,
                'alliance' => $this->corporation->alliance ? [
                    'alliance_id' => $this->corporation->alliance->alliance_id,
                    'name' => $this->corporation->alliance->name,
                ] : null,
            ],
            'stages' => $this->reviewRounds
                ->map(fn (EnlistmentReviewRound $round) => [
                    'position' => $round->position,
                    'label' => $round->label,
                ])
                ->values(),
        ];
    }
}
