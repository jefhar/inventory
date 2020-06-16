<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Generator as Faker;

$factory->define(
    WorkOrder::class,
    function (Faker $faker) {
        if (Client::count() < 6) {
            factory(Client::class)->create();
        }
        $client = Client::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();
        if ($user === null) {
            $user = factory(User::class)->create();
        }

        return [
            WorkOrder::CLIENT_ID => $client->id,
            WorkOrder::USER_ID => $user->id,
        ];
    }
);
