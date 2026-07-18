<?php

use Illuminate\Database\Migrations\Migration;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

return new class extends Migration
{
    /**
     * Lossless one-off backfill: the `; `-delimited enlistments.steps string becomes structured
     * review-round rows (one per step, role_id null → legacy flat-permission gate). Every enlistment
     * gets at least one round because the steps accessor defaults to ['Open'], so the review engine
     * always finds a round for round 0.
     */
    public function up(): void
    {
        Enlistments::query()->get()->each(function (Enlistments $enlistment) {
            foreach ($enlistment->steps as $position => $label) {
                EnlistmentReviewRound::query()->firstOrCreate(
                    [
                        'corporation_id' => $enlistment->corporation_id,
                        'position' => $position,
                    ],
                    [
                        'role_id' => null,
                        'label' => $label,
                    ],
                );
            }
        });
    }
};
