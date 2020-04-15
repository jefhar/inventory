<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Carts\DataTransferObjects\CartStoreObject;
use Domain\Carts\Actions\CartStoreAction;
use Domain\PendingSales\Actions\PendingSalesDestroyAction;
use Domain\PendingSales\Actions\PendingSalesStoreAction;
use Domain\PendingSales\Actions\PricePatchAction;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Faker\Factory;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class PendingSalesTest
 *
 * @package Tests\Unit
 */
class PendingSalesTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function canCreatePendingSale(): void
    {
        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();

        PendingSalesStoreAction::execute($cart, $product);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::CART_ID => $cart->id,
            ]
        );
    }

    /**
     * @test
     */
    public function canDestroyPendingSale(): void
    {
        $client = factory(Client::class)->make();
        $product = $this->createFullProduct();

        CartStoreAction::execute(
            CartStoreObject::fromRequest(
                [
                    'product_id' => $product->id,
                    Client::COMPANY_NAME => $client->company_name,
                ]
            )
        );

        PendingSalesDestroyAction::execute($product);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::CART_ID => null,
            ]
        );
    }

    /**
     * @test
     */
    public function canPatchProductPendingSaleToAddPrice(): void
    {
        $faker = Factory::create();
        $price = $faker->randomNumber();
        $product = $this->createFullProduct();

        PricePatchAction::execute($product, $price);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::PRICE => $price,
            ]
        );
    }
}
