<?php

use Faker\Generator as Faker;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Type;

$factory->define(CharacterAsset::class, function (Faker $faker) {

    return [
        'character_id' => factory(CharacterInfo::class),
        'item_id' => $faker->unique()->randomNumber(),
        'is_blueprint_copy' => false,
        'is_singleton' => $faker->boolean,
        'location_flag'  => $faker->randomElement(['Hangar', 'AssetSafety', 'Deliveries']),
        'location_id' => $faker->randomNumber(),
        'location_type' => $faker->randomElement(['station', 'solar_system', 'other']),
        'quantity' => $faker->randomDigit,
        'type_id' => factory(Type::class)
    ];
});

$factory->state(CharacterAsset::class, 'withName', function (Faker $faker) {
    return [
        'name'  => $faker->name,
    ];
});

$factory->state(CharacterAsset::class, 'withoutType', function (Faker $faker) {
    return [
        'type_id' => $faker->numberBetween(5,10000)
    ];
});
