<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\WorkOrder;
use Faker\Generator as Faker;

$factory->define(
    WorkOrder::class,
    function (Faker $faker) {
        $client = factory(Client::class)->create();
        $user = factory(User::class)->create();

        return [
            WorkOrder::CLIENT_ID => $client->id,
            WorkOrder::USER_ID => $user->id,
        ];
    }
);
