<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\WorkOrders\Client;
use Faker\Generator as Faker;

$factory->define(
    Client::class,
    function (Faker $faker) {
        return [
            Client::COMPANY_NAME => $faker->company,
        ];
    }
);
