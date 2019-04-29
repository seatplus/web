<?php

use Faker\Generator as Faker;
use Seatplus\Web\Models\Settings\GlobalSettings;

$factory->define(GlobalSettings::class, function (Faker $faker) {

    return [
        'id'       => 1,
        'name'     => $faker->name,
        'value'    => $faker->text(),
    ];
});
