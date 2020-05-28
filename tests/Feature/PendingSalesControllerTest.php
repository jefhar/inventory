<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\PendingSalesController;
use App\Carts\DataTransferObjects\CartStoreObject;
use Domain\Carts\Actions\CartStoreAction;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Support\CartStore;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class PendingSalesControllerTest
 *
 * @package Tests\Feature
 */
class PendingSalesControllerTest extends TestCase
{
    use FullObjects;
    use FullUsers;

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
                route(PendingSalesController::STORE_NAME),
                [Product::CART_ID => $cart->luhn, Product::ID => $product->luhn,]
            )
            ->assertCreated()
            ->assertJson(
                [
                    Product::CART_ID => $cart->id,
                    Product::ID => $product->id,
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
                route(PendingSalesController::STORE_NAME),
                [Product::CART_ID => $cart->id, Product::ID => $product->id,]
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
                CartStore::PRODUCT_ID => $product->id,
                CartStore::COMPANY_NAME => $client->company_name,

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
            ->delete(route(PendingSalesController::DESTROY_NAME, $product))
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
            ->delete(route(PendingSalesController::DESTROY_NAME, $product))
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
                route(PendingSalesController::STORE_NAME),
                [Product::CART_ID => $cart->luhn, Product::ID => $product->luhn]
            )
            ->assertCreated()
            ->assertJson(
                [
                    Product::CART_ID => $cart->id,
                    Product::ID => $product->id,
                ]
            );

        $secondCart = $this->makeFullCart();
        $cart->save();
        $salesRep->carts()->save($secondCart);

        $this->post(
            route(PendingSalesController::STORE_NAME),
            [Product::CART_ID => $cart->luhn, Product::ID => $product->luhn]
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
                    CartStore::PRODUCT_ID => $product->id,
                    CartStore::COMPANY_NAME => $client->company_name,
                ]
            )
        );
        $secondProduct = $this->createFullProduct();
        $this->post(
            route(PendingSalesController::STORE_NAME),
            [
                Product::CART_ID => $cart->luhn,
                Product::ID => $secondProduct->luhn,
            ]
        )->assertJson(
            [
                'cart_id' => $cart->id,
                'cart' => [
                    'client' => [
                        Client::COMPANY_NAME => $client->company_name,
                    ],
                ],
            ]
        );
    }
}
