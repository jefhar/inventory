<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Exceptions\InvalidAction;
use App\Admin\Permissions\UserRoles;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Carts\DataTransferObjects\CartStoreObject;
use App\User;
use Domain\Carts\Actions\CartDestroyAction;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Actions\CartStoreAction;
use Domain\Carts\CartInvoiced;
use Domain\Carts\Events\CartCreated;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
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

        $cart->refresh();
        $this->assertEquals($cart->user->name, $user->name);
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
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $savedCart = CartStoreAction::execute($cartStoreObject);

        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $savedCart->id,
                Cart::LUHN => $savedCart->luhn,
            ]
        )
            ->assertDatabaseHas(
                Product::TABLE,
                [
                    Product::ID => $product->id,
                    Product::CART_ID => $savedCart->id,
                ]
            );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function canDestroyOwnCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $cart = CartStoreAction::execute($cartStoreObject);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::CART_ID => $cart->id,
                Product::STATUS => Product::STATUS_IN_CART,
            ]
        );

        CartDestroyAction::execute($cart);
        $this->assertSoftDeleted($cart);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::STATUS => Cart::STATUS_VOID,
            ]
        );
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::CART_ID => null,
                Product::ID => $product->id,
                Product::STATUS => Product::STATUS_AVAILABLE,
            ]
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function cannotDestroySomeoneElsesCart(): void
    {
        $myCartUser = $this->createEmployee(UserRoles::SALES_REP);
        $notMyCartUser = $this->createEmployee(UserRoles::SALES_REP);
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $this->actingAs($myCartUser);
        $savedCart = CartStoreAction::execute($cartStoreObject);

        $this->actingAs($notMyCartUser);
        $this->expectException(AuthorizationException::class);
        CartDestroyAction::execute($savedCart);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function canPatchCartStatus(): void
    {
        /** @var Cart $cart */
        $cart = factory(Cart::class)->create();
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $salesRep->carts()->save($cart);
        $this->actingAs($salesRep);
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

    /**
     * @test
     */
    public function addingToCartUpdatesProductStatus(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $client = factory(Client::class)->create();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        CartStoreAction::execute($cartStoreObject);
        $product->refresh();
        $this->assertEquals(Product::STATUS_IN_CART, $product->status);
    }

    /**
     * @test
     */
    public function productMustExistBeforeBeingAddedToACartOrItThrowsException(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $client = factory(Client::class)->create();

        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => 1,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        CartStoreAction::execute($cartStoreObject);
    }

    /**
     * @test
     */
    public function updateCartToStatusInvoicedMarksCartInvoiced(): void
    {
        // setup open cart
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $client = $this->createFullClient();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $cart = CartStoreAction::execute($cartStoreObject);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::STATUS => CART::STATUS_OPEN,
            ]
        );

        // Test
        $cart = CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => Cart::STATUS_INVOICED]));
        $this->assertEquals(Cart::STATUS_INVOICED, $cart->status);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
                Cart::STATUS => CART::STATUS_INVOICED,
            ]
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function updateCartToStatusInvoicedMarksProductsInvoiced(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $client = $this->createFullClient();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $cart = CartStoreAction::execute($cartStoreObject);
        CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => Cart::STATUS_INVOICED]));
        $product->refresh();
        $this->assertEquals(Product::STATUS_INVOICED, $product->status);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::STATUS => Product::STATUS_INVOICED,
            ]
        );
    }

    /**
     * @test
     * @throws \Exception
     */
    public function updateCartToBadStatusThrowsException(): void
    {
        $this->expectException(InvalidAction::class);
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP));
        $client = factory(Client::class)->create();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $cart = CartStoreAction::execute($cartStoreObject);
        CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => 'flarp']));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function invoicingCartGeneratesEmailToSalesRep(): void
    {
        Mail::fake();
        Mail::assertNothingSent();
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);

        $client = $this->createFullClient();
        $product = $this->createFullProduct();
        $cartStoreObject = CartStoreObject::fromRequest(
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $cart = CartStoreAction::execute($cartStoreObject);

        CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => Cart::STATUS_INVOICED]));
        $cart->refresh();

        Mail::assertQueued(
            CartInvoiced::class,
            function ($mail) use ($salesRep, $cart) {
                return $mail->hasCC($salesRep->email) &&
                    $mail->hasTo($cart->client->person->email);
            }
        );
    }
}
