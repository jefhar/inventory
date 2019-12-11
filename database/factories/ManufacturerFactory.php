<?php

/** @var Factory $factory */

use Domain\Products\Models\Manufacturer;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(
    Manufacturer::class,
    function (Faker $faker) {
        return [
            Manufacturer::NAME => "O'" . $faker->unique()->company,
        ];
    }
);
