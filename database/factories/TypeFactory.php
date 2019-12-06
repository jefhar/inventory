<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\Products\Models\Type;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(
    Type::class,
    function (Faker $faker) {
        $name = $faker->catchPhrase;

        return [
            Type::NAME => $name,
            Type::SLUG => Str::slug($name),
        ];
    }
);
