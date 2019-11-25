<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\WorkOrders\Client;
use Domain\WorkOrders\WorkOrder;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Auth;

$factory->define(
    WorkOrder::class,
    function (Faker $faker) {
        $client = factory(Client::class)->create();

        return [
            WorkOrder::CLIENT_ID => $client->id,
            WorkOrder::USER_ID => 1,
        ];
    }
);
