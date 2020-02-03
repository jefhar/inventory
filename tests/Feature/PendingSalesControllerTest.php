<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\PendingSalesController;
use App\User;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class PendingSalesControllerTest
 *
 * @package Tests\Feature
 */
class PendingSalesControllerTest extends TestCase
{
    use FullObjects;

    private User $salesRep;

    /**
     * @test
     */
    public function salesRepCanAddProductToCart(): void
    {
        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();
        $this->salesRep->carts()->save($cart);

        $this->actingAs($this->salesRep)
            ->post(
                route(PendingSalesController::STORE_NAME),
                [
                    Product::CART_ID => $cart->id,
                    Product::ID => $product->id,
                ]
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
        /** @var User $technician */
        $technician = factory(User::class)->make();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->save();
        $cart = $this->makeFullCart();
        $product = $this->createFullProduct();
        $technician->carts()->save($cart);

        $this->actingAs($technician)
            ->post(
                route(PendingSalesController::STORE_NAME),
                [
                    Product::CART_ID => $cart->id,
                    Product::ID => $product->id,
                ]
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanRemoveProductFromCart(): void
    {
        $product = $this->createFullProduct();
        $cart = factory(Cart::class)->make();
        /** @var User $salesRep */
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $salesRep->carts()->save($cart);
        $cart->products()->save($product);

        $this->actingAs($salesRep)
            ->delete(
                route(PendingSalesController::DESTROY_NAME, $product)
            )
            ->assertOk();
    }

    /**
     * @test
     */
    public function technicianCantRemoveProductFromCart(): void
    {
        $product = $this->createFullProduct();
        $cart = factory(Cart::class)->make();
        /** @var User $technician */
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->carts()->save($cart);
        $cart->products()->save($product);

        $this->actingAs($technician)
            ->delete(
                route(PendingSalesController::DESTROY_NAME, $product)
            )
            ->assertForbidden();
    }

    public function setUp(): void
    {
        parent::setUp();
        /** @var User $salesRep */
        $salesRep = factory(User::class)->make();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $salesRep->save();
        $this->salesRep = $salesRep;
    }
}
