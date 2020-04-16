<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Permissions\UserRoles;
use App\Carts\DataTransferObjects\CartStoreObject;
use Domain\Carts\Actions\CartStoreAction;
use Domain\PendingSales\Actions\PendingSalesDestroyAction;
use Domain\PendingSales\Actions\PendingSalesStoreAction;
use Domain\PendingSales\Actions\PricePatchAction;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class PendingSalesTest
 *
 * @package Tests\Unit
 */
class PendingSalesTest extends TestCase
{
    use FullObjects;
    use FullUsers;

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
    public function cannotCreatePendingSaleIfProductIsNotAvailable(): void
    {
        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();
        PendingSalesStoreAction::execute($cart, $product);
        $this->expectException(HttpException::class);
        PendingSalesStoreAction::execute($cart, $product);

    }

    /**
     * @test
     */
    public function canDestroyPendingSale(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
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
        $product->refresh();    // CartStoreAction updated product in database.
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
    public function cannotDestroyPendingSaleIfProductIsNotInACart(): void
    {
        $this->expectException(HttpException::class);
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
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
        // $product->refresh();    // this `$product` is STATUS_AVAILABLE.
        PendingSalesDestroyAction::execute($product);

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
