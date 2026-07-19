<?php

namespace Seatplus\Web\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Web\Models\Recruitment\ApplicationRoundReview;

class ApplicationRoundReviewFactory extends Factory
{
    protected $model = ApplicationRoundReview::class;

    public function definition()
    {
        return [
            'application_id' => Application::factory(),
            'position' => 0,
            'role_id' => null,
            'causer_type' => User::class,
            'causer_id' => User::factory(),
            'decision' => $this->faker->randomElement(['accepted', 'rejected']),
            'comment' => null,
        ];
    }
}
