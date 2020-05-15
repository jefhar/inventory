<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\CartsController;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Products\Controllers\InventoryController;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class CartControllerTest
 *
 * @package Tests\Feature
 */
class CartsControllerTest extends TestCase
{
    use FullObjects;
    use FullUsers;

    /**
     * @test
     */
    public function salesRepCanCreateCart(): void
    {
        $cart = $this->makeFullCart();
        $product = $this->createFullProduct();
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->post(
                route(CartsController::STORE_NAME),
                [
                    'product_id' => $product->id,
                    Client::COMPANY_NAME => $cart->client->company_name,
                    Person::FIRST_NAME => $cart->client->person->first_name,
                ]
            )
            ->assertCreated()
            ->assertJson(
                [
                    Cart::ID => 1,
                    'client' => [
                        Client::COMPANY_NAME => $cart->client->company_name,
                    ],
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
            ->post(route(CartsController::STORE_NAME), $cart->toArray())
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanAccessCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->withoutMix()
            ->get(route(CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function ownerCanAccessCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::OWNER))
            ->withoutMix()
            ->get(route(CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function othersCantAccessCart(): void
    {
        $this->actingAs($this->createEmployee())
            ->get(route(CartsController::INDEX_NAME))
            ->assertForbidden();

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(CartsController::INDEX_NAME))
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

        $this->withoutMix()
            ->actingAs($salesRep)
            ->get(route(CartsController::SHOW_NAME, $cart))
            ->assertOk()
            ->assertSee(htmlspecialchars($cart->client->company_name, ENT_QUOTES | ENT_HTML401));
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
            route(CartsController::STORE_NAME),
            [
                'product_id' => $product->id,
                Client::COMPANY_NAME => $cart->client->company_name,
                Person::FIRST_NAME => $cart->client->person->first_name,
            ]
        );

        $cart = Cart::find(1);
        $this->delete(route(CartsController::DESTROY_NAME, $cart))
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
            ->delete(route(CartsController::DESTROY_NAME, $employeeCart))
            ->assertForbidden();

        /** @var Cart $employeeCart */
        $technicianCart = factory(Cart::class)->make();
        $technician = $this->createEmployee(UserRoles::TECHNICIAN);
        $technician->carts()->save($technicianCart);

        $this->actingAs($technician)
            ->delete(route(CartsController::DESTROY_NAME, $employeeCart))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanUpdateCartStatus(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);
        $voidingCart = $this->makeFullCart();
        $salesRep->carts()->save($voidingCart);
        $this
            ->patch(
                route(CartsController::UPDATE_NAME, $voidingCart),
                [Cart::STATUS => Cart::STATUS_VOID]
            )
            ->assertOk()
            ->assertJson(
                [
                    Cart::ID => $voidingCart->id,
                    Cart::STATUS => Cart::STATUS_VOID,

                ]
            );

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
            route(CartsController::UPDATE_NAME, $invoicingCart),
            [Cart::STATUS => Cart::STATUS_INVOICED]
        );

        $response->assertOk()
            ->assertJson(
                [
                    Cart::ID => $invoicingCart->id,
                    Cart::STATUS => Cart::STATUS_INVOICED,
                ]
            );
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
            ->get(route(CartsController::SHOW_NAME, $cart))
            ->assertSee($cart->status);

        $cart->status = Cart::STATUS_INVOICED;
        $cart->save();
        $this
            ->get(route(CartsController::SHOW_NAME, $cart))
            ->assertSee(Str::title($cart->status));

        $cart->status = Cart::STATUS_VOID;
        $cart->save();
        $this
            ->get(route(CartsController::SHOW_NAME, $cart))
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
            ->get(route(CartsController::SHOW_NAME, $cart));
        for ($i = 0; $i < 20; ++$i) {
            $response->assertSeeText(htmlspecialchars($products[$i]->manufacturer->name, ENT_QUOTES | ENT_HTML401));
            $response->assertSeeText(htmlspecialchars($products[$i]->model, ENT_QUOTES | ENT_HTML401));
        }
    }

    /**
     * @test
     */
    public function showInvoicedCartDisplaysProducts(): void
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
        CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => Cart::STATUS_INVOICED]));

        $response = $this
            ->actingAs($salesRep)
            ->get(route(CartsController::SHOW_NAME, $cart));
        for ($i = 0; $i < 20; ++$i) {
            $this->assertDatabaseHas(
                Product::TABLE,
                [
                    Product::ID => $products[$i]->id,
                    Product::STATUS => Product::STATUS_INVOICED,
                ]
            );
            $response->assertSeeText(htmlspecialchars($products[$i]->manufacturer->name, ENT_QUOTES | ENT_HTML401));
            $response->assertSeeText(htmlspecialchars($products[$i]->model, ENT_QUOTES | ENT_HTML401));
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
        CartPatchAction::execute($cart, CartPatchObject::fromRequest([Cart::STATUS => Cart::STATUS_VOID]));

        $response = $this
            ->actingAs($salesRep)
            ->get(route(CartsController::SHOW_NAME, $cart));
        for ($i = 0; $i < 20; ++$i) {
            $response->assertDontSeeText(htmlspecialchars($products[$i]->manufacturer->name, ENT_QUOTES | ENT_HTML401));
            $response->assertDontSeeText(htmlspecialchars($products[$i]->model, ENT_QUOTES | ENT_HTML401));
        }
    }
}
