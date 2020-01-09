<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\User;
use Domain\WorkOrders\Models\Client;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class CartTest
 *
 * @package Tests\Unit
 */
class CartTest extends TestCase
{
    /**
     * @test
     */
    public function createdCartThrowsEvent(): void
    {
        Event::fake();
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $cart->save();
        Event::assertDispatched(
            \Domain\Carts\Events\CartOrderCreated::class,
            function ($e) use ($cart) {
                return $e->cart->id === $cart->id;
            }
        );
    }

    /**
     * @test
     */
    public function cartCreatesItsOwnLuhn(): void
    {
        /** \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $this->assertArrayNotHasKey(\Domain\Carts\Models\Cart::LUHN, $cart->toArray());
        $cart->save();
        $this->assertDatabaseHas(
            \Domain\Carts\Models\Cart::TABLE,
            [
                \Domain\Carts\Models\Cart::ID => $cart->id,
                \Domain\Carts\Models\Cart::LUHN => $cart->luhn,
            ]
        );
    }

    /**
     * @test
     */
    public function cartBelongsToUser(): void
    {
        $user = factory(User::class)->create();
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $user->carts()->save($cart);
        $this->assertDatabaseHas(
            \Domain\Carts\Models\Cart::TABLE,
            [
                \Domain\Carts\Models\Cart::ID => $cart->id,
                \Domain\Carts\Models\Cart::USER_ID => $user->id,
            ]
        );
    }

    /**
     * @test
     */
    public function cartBelongsToAClient(): void
    {
        $user = factory(User::class)->create();
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $client = factory(Client::class)->create();
        $user->carts()->save($cart);
        $client->carts()->save($cart);
        $this->assertDatabaseHas(
            \Domain\Carts\Models\Cart::TABLE,
            [
                \Domain\Carts\Models\Cart::ID => $cart->id,
                \Domain\Carts\Models\Cart::CLIENT_ID => $client->id,
            ]
        );
    }

    /**
     * @test
     */
    public function canCreateCart(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $cartStoreObject = \App\Carts\DataTransferObjects\CartStoreObject::fromRequest($cart->toArray());
        $savedCart = \Domain\Carts\Actions\CartStoreAction::execute($cartStoreObject);

        $this->actingAs(factory(User::class)->create());
        $this->assertDatabaseHas(
            \Domain\Carts\Models\Cart::TABLE,
            [
                \Domain\Carts\Models\Cart::ID => $savedCart->id,
                \Domain\Carts\Models\Cart::LUHN => $savedCart->luhn,
            ]
        );
    }

    /**
     * @test
     */
    public function canDestroyCart(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->create();

        \Domain\Carts\Action\CartDestroyAction::execute($cart);
        $this->assertSoftDeleted($cart);
    }

    /**
     * @test
     */
    public function canPatchCartStatus(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->create();

        $cartPatchObject = \Domain\Carts\DataTransferObjects\CartPatchObject::fromRequest(
            [
                \Domain\Carts\Models\Cart::STATUS => \Domain\Carts\Models\Cart::STATUS_VOID,
            ]
        );
        \Domain\Carts\Actions\CartPatchAction::execute($cart, $cartPatchObject);
        $this->assertDatabaseHas(
            \Domain\Carts\Models\Cart::TABLE,
            [
                \Domain\Carts\Models\Cart::ID => $cart->id,
                \Domain\Carts\Models\Cart::LUHN => $cart->luhn,
                \Domain\Carts\Models\Cart::STATUS => \Domain\Carts\Models\Cart::STATUS_VOID,
            ]
        );
    }
}
