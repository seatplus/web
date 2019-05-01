<?php


use Faker\Generator as Faker;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

$factory->define(CharacterInfo::class, function (Faker $faker) {

    return [
        'character_id'    => $faker->numberBetween(9000000, 98000000),
        'name'            => $faker->name,
        'corporation_id'  => $faker->numberBetween(98000000, 99000000),
        'birthday'        => $faker->iso8601($max = 'now'),
        'gender'          => $faker->randomElement(['male', 'female']),
        'race_id'         => $faker->randomDigitNotNull,
        'bloodline_id'    => $faker->randomDigitNotNull,
    ];
});
