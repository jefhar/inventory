<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Permissions\UserRoles;
use App\Carts\DataTransferObjects\CartStoreObject;
use App\Carts\Requests\CartStoreRequest;
use Domain\Carts\Actions\CartStoreAction;
use Domain\PendingSales\Actions\PendingSalesDestroyAction;
use Domain\PendingSales\Actions\PendingSalesStoreAction;
use Domain\PendingSales\Actions\PricePatchAction;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
    public function cannotCreatePendingSaleIfProductIsNotAvailable(): void
    {
        $this->expectException(HttpException::class);

        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();
        PendingSalesStoreAction::execute($cart, $product);
        PendingSalesStoreAction::execute($cart, $product);
    }

    /**
     * @test
     */
    public function canDestroyPendingSale(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        /** @var Client $client */
        $client = factory(Client::class)->make();
        $product = $this->createFullProduct();

        CartStoreAction::execute(
            CartStoreObject::fromRequest(
                [
                    CartStoreRequest::PRODUCT_ID => $product->luhn,
                    CartStoreRequest::CLIENT_COMPANY_NAME => $client->company_name,
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
        /** @var Client $client */
        $client = factory(Client::class)->make();
        $product = $this->createFullProduct();

        CartStoreAction::execute(
            CartStoreObject::fromRequest(
                [
                    CartStoreRequest::PRODUCT_ID => $product->luhn,
                    CartStoreRequest::CLIENT_COMPANY_NAME => $client->company_name,
                ]
            )
        );
        // this `$product` is STATUS_AVAILABLE. Do not refresh().
        PendingSalesDestroyAction::execute($product);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function canPatchProductPendingSaleToAddPrice(): void
    {
        $price = rand(0, 999_999_99);
        // $price = 31800422;
        $product = $this->createFullProduct();

        PricePatchAction::execute($product, $price / 100);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::PRICE => $price,
            ]
        );
    }
}
