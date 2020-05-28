<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Traits;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;

/**
 * Trait FullObjects
 *
 * @package Tests\Unit\Tests\Traits
 */
trait FullObjects
{
    /**
     * @return Product
     */
    private function createFullProduct(): Product
    {
        $faker = Factory::create();
        $manufacturerName = $faker->company;
        $workOrder = factory(WorkOrder::class)->create();
        /** @var Product $product */
        $product = factory(Product::class)->make();
        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $manufacturerName]);
        $product->manufacturer()->associate($manufacturer);
        $product->workOrder()->associate($workOrder);
        $product->save();

        return $product;
    }

    /**
     * @return Cart
     */
    private function makeFullCart(): Cart
    {
        $person = factory(Person::class)->make();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();
        $client->person()->save($person);
        $cart->client()->associate($client);

        return $cart;
    }

    private function createFullClient(): Client
    {
        $person = factory(Person::class)->make();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $client->person()->save($person);
        $client->save();
        return $client;
    }
}
