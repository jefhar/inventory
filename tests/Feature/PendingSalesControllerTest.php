<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\Products\Models\Product;
use Tests\TestCase;

/**
 * Class PendingSalesControllerTest
 *
 * @package Tests\Feature
 */
class PendingSalesControllerTest extends TestCase
{
    private User $salesRep;

    /**
     * @test
     */
    public function salesRepCanAddProductToCart(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        /** @var Product $product */
        $product = factory(Product::class)->create();
        $this->salesRep->carts()->save($cart);

        $this->actingAs($this->salesRep)
            ->post(
                route(\App\PendingSales\Controllers\PendingSalesController),
                [
                    Product::ID => $product->luhn,
                    \Domain\Carts\Models\Cart::ID => $cart->luhn,
                ]
            )
            ->assertCreated()
            ->assertJson(
                [
                    Product::ID => $product->luhn,
                    \Domain\Carts\Models\Cart::ID => $cart->luhn,
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
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        /** @var Product $product */
        $product = factory(Product::class)->create();
        $technician->carts()->save($cart);

        $this->actingAs($this->salesRep)
            ->post(
                route(\App\PendingSales\Controllers\PendingSalesController),
                [
                    Product::ID => $product->luhn,
                    \Domain\Carts\Models\Cart::ID => $cart->luhn,
                ]
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanRemoveProductFromCart(): void
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();
        /** @var \Domain\Carts\Model\Cart $cart */
        $cart = factory(\Domain\Carts\Model\Cart::class)->make();
        /** @var User $salesRep */
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $salesRep->carts()->save($cart);
        $cart->products()->save($product);

        $this->actingAs($salesRep)
            ->delete(
                route(\App\PendingSales\Controllers\PendingSalesController::DESTROY_NAME),
                [
                    \Domain\Carts\Models\Cart::ID => $cart->luhn,
                    Product::ID => $product->luhn
                ]
            )
            ->assertOk();
    }

    /**
     * @test
     */
    public function technicianCantRemoveProductFromCart(): void
    {
        /** @var Product $product */
        $product = factory(Product::class)->create();
        /** @var \Domain\Carts\Model\Cart $cart */
        $cart = factory(\Domain\Carts\Model\Cart::class)->make();
        /** @var User $technician */
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->carts()->save($cart);
        $cart->products()->save($product);

        $this->actingAs($technician)
            ->delete(
                route(\App\PendingSales\Controllers\PendingSalesController::DESTROY_NAME),
                [
                         \Domain\Carts\Models\Cart::ID => $cart->luhn,
                         Product::ID => $product->luhn
                ]
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
