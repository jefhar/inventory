<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Faker\Generator as Faker;

$factory->define(
    Product::class,
    function (Faker $faker) {
        if (Manufacturer::count() < 20) {
            Manufacturer::create([Manufacturer::NAME => $faker->company]);
        }

        if (Type::count() < 8) {
            factory(Type::class)->create();
        }

        $manufacturer = Manufacturer::inRandomOrder()->first();
        $type = Type::inRandomOrder()->first();

        return [
            Product::MODEL => $faker->jobTitle,
            Product::MANUFACTURER_ID => $manufacturer->id,
            Product::TYPE_ID => $type->id,
            Product::VALUES=> [
                'serial' => $faker->isbn10
            ]
        ];
    }
);
