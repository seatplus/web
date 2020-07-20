<?php

use Faker\Generator as Faker;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

$factory->define(CharacterAffiliation::class, function (Faker $faker) {

    return [
        'character_id'    => factory(CharacterInfo::class),
        'corporation_id'  => factory(CorporationInfo::class),
        'alliance_id'     => $faker->optional()->numberBetween(99000000,100000000),
        'faction_id'     => $faker->optional()->numberBetween(500000,1000000),
        'last_pulled' => $faker->dateTime()
    ];
});
