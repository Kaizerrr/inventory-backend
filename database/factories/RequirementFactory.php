<?php

namespace Database\Factories;

use App\Models\Requirement;
use Faker\Generator as Faker;

$factory->define(Requirement::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'codeName' => $faker->word,
    ];
});