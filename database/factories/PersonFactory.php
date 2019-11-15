<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\WorkOrders\Person;
use Faker\Generator as Faker;

$factory->define(
    Person::class,
    function (Faker $faker) {
        return [
            Person::EMAIL => $faker->email,
            Person::FIRST_NAME => $faker->firstName,
            Person::LAST_NAME => $faker->lastName,
            Person::PHONE_NUMBER => Person::unformatPhoneNumber($faker->phoneNumber),
        ];
    }
);
