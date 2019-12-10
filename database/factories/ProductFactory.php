<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Faker\Generator as Faker;

$factory->define(
    Product::class,
    function (Faker $faker) {
        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $faker->company]);
        $type = factory(Type::class)->create();

        return [
            Product::MODEL => $faker->word,
            Product::MANUFACTURER_ID => $manufacturer->id,
            Product::TYPE_ID => $type->id,
        ];
    }
);
