<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Permissions\UserRoles;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Carts\DataTransferObjects\CartStoreObject;
use App\User;
use Domain\Carts\Actions\CartDestroyAction;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Actions\CartStoreAction;
use Domain\Carts\Events\CartCreated;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class CartTest
 *
 * @package Tests\Unit
 */
class CartTest extends TestCase
{
    use FullObjects;
    use FullUsers;

    /**
     * @test
     */
    public function createdCartThrowsEvent(): void
    {
        Event::fake();
        $cart = factory(Cart::class)->make();
        $cart->save();
        Event::assertDispatched(
            CartCreated::class,
            function ($e) use ($cart) {
                return $e->cart->id === $cart->id;
            }
        );
    }

    /**
     * @test
     */
    public function createdCartReturnsCompanyName(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $client = factory(Client::class)->create();
        $cartStoreObject = CartStoreObject::fromRequest([
            Client::COMPANY_NAME => $client->company_name
                                                        ]);
        $cart = CartStoreAction::execute($cartStoreObject);
        $this->assertEquals($client->company_name, $cart->client->company_name);
    }

    /**
     * @test
     */
    public function cartCreatesItsOwnLuhn(): void
    {
        /** \Domain\Carts\Models\Cart $cart */
        $cart = factory(Cart::class)->make();
        $this->assertArrayNotHasKey(Cart::LUHN, $cart->toArray());
        $cart->save();
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::LUHN => $cart->luhn,
            ]
        );
    }

    /**
     * @test
     */
    public function cartBelongsToUser(): void
    {
        $user = factory(User::class)->create();
        $cart = factory(Cart::class)->make();
        $user->carts()->save($cart);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::USER_ID => $user->id,
            ]
        );
    }

    /**
     * @test
     */
    public function cartBelongsToAClient(): void
    {
        $user = factory(User::class)->create();
        $cart = factory(Cart::class)->make();
        $client = factory(Client::class)->create();
        $user->carts()->save($cart);
        $client->carts()->save($cart);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::CLIENT_ID => $client->id,
            ]
        );
    }

    /**
     * @test
     */
    public function canCreateCart(): void
    {
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();
        $cartStoreObject = CartStoreObject::fromRequest($cart->toArray());
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $savedCart = CartStoreAction::execute($cartStoreObject);

        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $savedCart->id,
                Cart::LUHN => $savedCart->luhn,
            ]
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function canDestroyCart(): void
    {
        $product = $this->createFullProduct();
        $cart = $this->makeFullCart();
        $cart->save();
        $cart->products()->save($product);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::CART_ID => $cart->id,
            ]
        );
        CartDestroyAction::execute($cart);
        $this->assertSoftDeleted($cart);
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
    public function canPatchCartStatus(): void
    {
        /** @var Cart $cart */
        $cart = factory(Cart::class)->create();

        $cartPatchObject = CartPatchObject::fromRequest(
            [
                Cart::STATUS => Cart::STATUS_VOID,
            ]
        );
        CartPatchAction::execute($cart, $cartPatchObject);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::LUHN => $cart->luhn,
                Cart::STATUS => Cart::STATUS_VOID,
            ]
        );
    }
}
