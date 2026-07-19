<?php

namespace Seatplus\Web\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

class EnlistmentReviewRoundFactory extends Factory
{
    protected $model = EnlistmentReviewRound::class;

    public function definition()
    {
        return [
            // Create the parent enlistment (posting) so the FK holds; tests usually override corporation_id.
            'corporation_id' => function () {
                $corporation = CorporationInfo::factory()->create();

                Enlistments::query()->firstOrCreate(
                    ['corporation_id' => $corporation->corporation_id],
                    ['type' => 'character'],
                );

                return $corporation->corporation_id;
            },
            'position' => 0,
            'role_id' => null,
            'label' => $this->faker->word(),
        ];
    }
}
