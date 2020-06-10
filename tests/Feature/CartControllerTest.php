<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\CartController;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Carts\Requests\CartPatchRequest;
use App\Carts\Requests\CartStoreRequest;
use App\Carts\Resources\CartResource;
use App\Products\Controllers\InventoryController;
use App\Support\Luhn;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class CartControllerTest
 *
 * @package Tests\Feature
 */
class CartControllerTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function salesRepCanCreateCart(): void
    {
        $cart = $this->makeFullCart();
        $product = $this->createFullProduct();
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->post(
                route(CartController::STORE_NAME),
                [
                    CartStoreRequest::PRODUCT_ID => $product->luhn,
                    CartStoreRequest::CLIENT_COMPANY_NAME => $cart->client->company_name,
                    CartStoreRequest::FIRST_NAME => $cart->client->person->first_name,
                ]
            )
            ->assertCreated()
            ->assertJson(
                [
                    CartResource::CLIENT_COMPANY_NAME => $cart->client->company_name,
                    CartResource::CART_ID => Luhn::create(1),
                ]
            );
    }

    /**
     * @test
     */
    public function technicianCantCreateCart(): void
    {
        $cart = factory(Cart::class)->make();
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->post(route(CartController::STORE_NAME), $cart->toArray())
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanAccessCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->withoutMix()
            ->get(route(CartController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function ownerCanAccessCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::OWNER))
            ->withoutMix()
            ->get(route(CartController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function othersCantAccessCart(): void
    {
        $this->actingAs($this->createEmployee())
            ->withoutMix()
            ->get(route(CartController::INDEX_NAME))
            ->assertForbidden();

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->withoutMix()
            ->get(route(CartController::INDEX_NAME))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanSeeCart(): void
    {
        $cart = $this->makeFullCart();
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $salesRep->carts()->save($cart);

        $this->actingAs($salesRep)
            ->withoutMix()
            ->get(route(CartController::SHOW_NAME, $cart))
            ->assertOk()
            ->assertSee($cart->client->company_name);
    }

    /**
     * @test
     */
    public function salesRepCanDestroyOwnCart(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);

        $cart = $this->makeFullCart();
        $product = $this->createFullProduct();
        $this->post(
            route(CartController::STORE_NAME),
            [
                CartStoreRequest::PRODUCT_ID => $product->luhn,
                CartStoreRequest::CLIENT_COMPANY_NAME => $cart->client->company_name,
                CartStoreRequest::FIRST_NAME => $cart->client->person->first_name,
            ]
        );

        $cart = Cart::find(1);
        $this->delete(route(CartController::DESTROY_NAME, $cart))
            ->assertOk();
        $this->assertSoftDeleted(
            Cart::TABLE,
            [
                Cart::ID => $cart->id,
            ]
        );
        $this->assertDatabaseMissing(
            Product::TABLE,
            [
                Product::CART_ID => $cart->id,
            ]
        );
    }

    /**
     * @test
     */
    public function othersCantDestroyCart(): void
    {
        /** @var Cart $employeeCart */
        $employeeCart = factory(Cart::class)->make();
        $employee = $this->createEmployee();
        $employee->carts()->save($employeeCart);

        $this->actingAs($employee)
            ->delete(route(CartController::DESTROY_NAME, $employeeCart))
            ->assertForbidden();

        /** @var Cart $employeeCart */
        $technicianCart = factory(Cart::class)->make();
        $technician = $this->createEmployee(UserRoles::TECHNICIAN);
        $technician->carts()->save($technicianCart);

        $this->actingAs($technician)
            ->delete(route(CartController::DESTROY_NAME, $employeeCart))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanUpdateCartStatus(): void
    {
        Mail::fake();
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);
        $voidingCart = $this->makeFullCart();
        $salesRep->carts()->save($voidingCart);
        $this
            ->patch(
                route(CartController::UPDATE_NAME, $voidingCart),
                [CartPatchRequest::STATUS => Cart::STATUS_VOID]
            )
            ->assertOk()
            ->assertJson([CartResource::CART_ID => $voidingCart->luhn])
            ->assertJson([CartResource::STATUS => Cart::STATUS_VOID]);

        $invoicingCart = $this->makeFullCart();
        $salesRep->carts()->save($invoicingCart);
        $this->assertDatabaseHas(
            Cart::TABLE,
            [
                Cart::ID => $invoicingCart->id,
                Cart::STATUS => Cart::STATUS_OPEN,
            ]
        );

        $response = $this->patch(
            route(CartController::UPDATE_NAME, $invoicingCart),
            [CartPatchRequest::STATUS => Cart::STATUS_INVOICED]
        );

        $response->assertOk()
            ->assertJson([CartResource::CART_ID => $invoicingCart->luhn])
            ->assertJson([CartResource::STATUS => Cart::STATUS_INVOICED]);
    }

    /**
     * @test
     */
    public function cartIndexShowsProductModels(): void
    {
        $cart = $this->makeFullCart();
        $cart->save();
        $product = $this->createFullProduct();
        $otherProduct = $this->createFullProduct();
        $cart->products()->saveMany([$product, $otherProduct]);
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $salesRep->carts()->save($cart);
        $cart->load('products');
        $this
            ->actingAs($salesRep)
            ->withoutMix()
            ->get(route(InventoryController::INDEX_NAME))
            ->assertSeeText($cart->products[0]->model)
            ->assertSeeText($cart->products[1]->model);
    }

    /**
     * @test
     */
    public function showCartDisplaysCartStatus(): void
    {
        // Setup
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $product = $this->createFullProduct();
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $cart->products()->save($product);
        $cart->status = Cart::STATUS_OPEN;
        $cart->save();

        // Test
        $this->actingAs($salesRep)
            ->withoutMix()
            ->get(route(CartController::SHOW_NAME, $cart))
            ->assertSee($cart->status);

        $cart->status = Cart::STATUS_INVOICED;
        $cart->save();
        $this
            ->get(route(CartController::SHOW_NAME, $cart))
            ->assertSee(Str::title($cart->status));

        $cart->status = Cart::STATUS_VOID;
        $cart->save();
        $this
            ->get(route(CartController::SHOW_NAME, $cart))
            ->assertSee($cart->status);
    }

    /**
     * @test
     */
    public function showOpenCartDisplaysProducts(): void
    {
        // Setup
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $products = [];
        for ($i = 0; $i < 20; ++$i) {
            $products[] = $this->createFullProduct();
        }
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $cart->products()->saveMany($products);
        $cart->status = Cart::STATUS_OPEN;
        $cart->save();
        $response = $this
            ->actingAs($salesRep)
            ->withoutMix()
            ->get(route(CartController::SHOW_NAME, $cart));
        for ($i = 0; $i < 20; ++$i) {
            $response->assertSee($products[$i]->manufacturer->name);
            $response->assertSee($products[$i]->model);
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function showInvoicedCartDisplaysProducts(): void
    {
        Mail::fake();

        // Setup
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);
        $products = [];
        for ($i = 0; $i < 20; ++$i) {
            $products[] = $this->createFullProduct();
        }
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $cart->products()->saveMany($products);
        CartPatchAction::execute(
            $cart,
            CartPatchObject::fromRequest(
                [CartPatchRequest::STATUS => Cart::STATUS_INVOICED]
            )
        );

        $response = $this
            ->actingAs($salesRep)
            ->withoutMix()
            ->get(route(CartController::SHOW_NAME, $cart));
        for ($i = 0; $i < 20; ++$i) {
            $this->assertDatabaseHas(
                Product::TABLE,
                [
                    Product::ID => $products[$i]->id,
                    Product::STATUS => Product::STATUS_INVOICED,
                ]
            );
            $response->assertSeeText($products[$i]->manufacturer->name);
            $response->assertSeeText($products[$i]->model);
        }
    }

    /**
     * @test
     * @throws \Exception
     */
    public function showVoidedCartDisplaysNoProducts(): void
    {
        // Setup
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);
        $products = [];
        for ($i = 0; $i < 20; ++$i) {
            $products[] = $this->createFullProduct();
        }
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $cart->products()->saveMany($products);
        CartPatchAction::execute(
            $cart,
            CartPatchObject::fromRequest(
                [CartPatchRequest::STATUS => Cart::STATUS_VOID]
            )
        );

        $response = $this
            ->actingAs($salesRep)
            ->withoutMix()
            ->get(route(CartController::SHOW_NAME, $cart));
        for ($i = 0; $i < 20; ++$i) {
            $response->assertDontSeeText($products[$i]->manufacturer->name);
            $response->assertDontSeeText($products[$i]->model);
        }
    }
}
