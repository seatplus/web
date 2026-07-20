<?php

namespace Seatplus\Web\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Web\Models\Recruitment\ApplicationGroupMember;

class ApplicationGroupMemberFactory extends Factory
{
    protected $model = ApplicationGroupMember::class;

    public function definition()
    {
        return [
            'group_id' => (string) Str::uuid(),
            'application_id' => Application::factory(),
        ];
    }
}
