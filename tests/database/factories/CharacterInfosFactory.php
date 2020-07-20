<?php


use Faker\Generator as Faker;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\RefreshToken;

$factory->define(CharacterInfo::class, function (Faker $faker) {

    return [
        'character_id'    => $faker->numberBetween(9000000, 98000000),
        'name'            => $faker->name,
        'birthday'        => $faker->iso8601($max = 'now'),
        'gender'          => $faker->randomElement(['male', 'female']),
        'race_id'         => $faker->randomDigitNotNull,
        'bloodline_id'    => $faker->randomDigitNotNull,
    ];
});

$factory->afterCreating(CharacterInfo::class, function ($character, $faker) {
    factory(RefreshToken::class)->create([
        'character_id' => $character->character_id
    ]);
    //$user->refresh()->character_users()->character()->save(factory(CharacterInfoAlias::class)->create
});
