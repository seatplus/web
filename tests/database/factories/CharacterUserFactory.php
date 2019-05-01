<?php

use Faker\Generator as Faker;
use Seatplus\Web\Models\CharacterUser;
use Seatplus\Web\Models\User;

$factory->define(CharacterUser::class, function (Faker $faker) {

    return [
        'user_id'               => $faker->numberBetween(90000000, 98000000),
        'character_id'         => $faker->numberBetween(90000000, 98000000),
        'character_owner_hash' => sha1($faker->text),
    ];
});