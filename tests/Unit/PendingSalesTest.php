<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Products\Models\Product;
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
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->create();
        /** @var Product $product */
        $product = $this->createFullProduct();

        \Domain\PendingSales\Actions\CreatePendingSaleAction::execute($cart, $product);
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
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->create();
        /** @var Product $product */
        $product = $this->createFullProduct();

        $cart->products()->save($product);

        \Domain\PendingSales\Actions\DestroyPendingSalesAction::execute($product);
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
        /** @var Product $product */
        $product = $this->createFullProduct();

        \Domain\PendingSales\Actions\PricePatchAction::execute($product, $price);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::PRICE => $price,
            ]
        );
    }
}
