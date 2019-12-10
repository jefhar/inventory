<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Domain\Products\Models\Manufacturer;
use Faker\Generator as Faker;

$factory->define(
    Manufacturer::class,
    function (Faker $faker) {
        return [
            Manufacturer::NAME => $faker->company,
        ];
    }
);
