<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\PendingSaleController;
use App\Carts\DataTransferObjects\CartStoreObject;
use App\Carts\Requests\CartStoreRequest;
use App\Carts\Requests\PendingSalesStoreRequest;
use App\Products\Resources\ProductResource;
use Domain\Carts\Actions\CartStoreAction;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class PendingSalesControllerTest
 *
 * @package Tests\Feature
 */
class PendingSaleControllerTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function salesRepCanAddProductToCart(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();
        $salesRep->carts()->save($cart);

        $this->actingAs($salesRep)
            ->post(
                route(PendingSaleController::STORE_NAME),
                [
                    PendingSalesStoreRequest::CART_ID => $cart->luhn,
                    PendingSalesStoreRequest::PRODUCT_ID => $product->luhn,
                ]
            )
            ->assertCreated()
            ->assertJson(
                [
                    ProductResource::CART_ID => $cart->luhn,
                    ProductResource::PRODUCT_ID => $product->luhn,
                ]
            );
    }

    /**
     * @test
     */
    public function technicianCannotAddProductToCart(): void
    {
        $technician = $this->createEmployee(UserRoles::TECHNICIAN);
        $cart = $this->makeFullCart();
        $product = $this->createFullProduct();
        $technician->carts()->save($cart);

        $this->actingAs($technician)
            ->post(
                route(PendingSaleController::STORE_NAME),
                [
                    PendingSalesStoreRequest::CART_ID => $cart->luhn,
                    PendingSalesStoreRequest::PRODUCT_ID => $product->luhn,
                ]
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanRemoveProductFromCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                CartStoreRequest::PRODUCT_ID => $product->luhn,
                CartStoreRequest::CLIENT_COMPANY_NAME => $client->company_name,

            ]
        );
        CartStoreAction::execute($cartStoreObject);
        $product->refresh();
        $this->assertDatabaseMissing(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::STATUS => Product::STATUS_AVAILABLE,
            ]
        );

        $this
            ->delete(route(PendingSaleController::DESTROY_NAME, $product))
            ->assertOk();
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::STATUS => Product::STATUS_AVAILABLE,
            ]
        );
    }

    /**
     * @test
     */
    public function technicianCantRemoveProductFromCart(): void
    {
        $product = $this->createFullProduct();
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();
        $technician = $this->createEmployee(UserRoles::TECHNICIAN);
        $technician->carts()->save($cart);
        $cart->products()->save($product);

        $this->actingAs($technician)
            ->delete(route(PendingSaleController::DESTROY_NAME, $product))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function cannotAddSameProductToASecondCart(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();
        $salesRep->carts()->save($cart);

        $this->actingAs($salesRep)
            ->post(
                route(PendingSaleController::STORE_NAME),
                [
                    PendingSalesStoreRequest::CART_ID => $cart->luhn,
                    PendingSalesStoreRequest::PRODUCT_ID => $product->luhn,
                ]
            )
            ->assertCreated()
            ->assertJson(
                [
                    ProductResource::CART_ID => $cart->luhn,
                    ProductResource::PRODUCT_ID => $product->luhn,
                ]
            );

        $secondCart = $this->makeFullCart();
        $cart->save();
        $salesRep->carts()->save($secondCart);

        $this->post(
            route(PendingSaleController::STORE_NAME),
            [
                PendingSalesStoreRequest::CART_ID => $cart->luhn,
                PendingSalesStoreRequest::PRODUCT_ID => $product->luhn,
            ]
        )
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function addingProductToExistingCartReturnsCartIdAndClient(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $product = $this->createFullProduct();
        /** @var Client $client */
        $client = factory(Client::class)->make();
        $cart = CartStoreAction::execute(
            CartStoreObject::fromRequest(
                [
                    CartStoreRequest::PRODUCT_ID => $product->luhn,
                    CartStoreRequest::CLIENT_COMPANY_NAME => $client->company_name,
                ]
            )
        );
        $secondProduct = $this->createFullProduct();
        $this->post(
            route(PendingSaleController::STORE_NAME),
            [
                PendingSalesStoreRequest::CART_ID => $cart->luhn,
                PendingSalesStoreRequest::PRODUCT_ID => $secondProduct->luhn,
            ]
        )->assertJson(
            [
                ProductResource::CART_ID => $cart->luhn,
                ProductResource::CLIENT_COMPANY_NAME => $client->company_name,
            ]
        );
    }
}
