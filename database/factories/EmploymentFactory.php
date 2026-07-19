<?php

namespace Seatplus\Web\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Enums\EmploymentStatus;
use Seatplus\Web\Models\Employment\Employment;

class EmploymentFactory extends Factory
{
    protected $model = Employment::class;

    public function definition()
    {
        return [
            'subject_type' => User::class,
            'subject_id' => User::factory(),
            'corporation_id' => CorporationInfo::factory(),
            'application_id' => null,
            'status' => EmploymentStatus::Active,
            'hired_at' => now(),
            'ended_at' => null,
        ];
    }

    public function character(): static
    {
        return $this->state(fn () => [
            'subject_type' => CharacterInfo::class,
            'subject_id' => CharacterInfo::factory(),
        ]);
    }

    public function alumni(): static
    {
        return $this->state(fn () => [
            'status' => EmploymentStatus::Alumni,
            'ended_at' => now(),
        ]);
    }
}
