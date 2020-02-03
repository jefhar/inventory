<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\CartsController;
use Domain\Carts\Models\Cart;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class CartControllerTest
 *
 * @package Tests\Feature
 */
class CartControllerTest extends TestCase
{
    use FullObjects;
    use FullUsers;

    /**
     * @test
     */
    public function salesRepCanCreateCart(): void
    {
        $cart = factory(Cart::class)->make();
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->post(route(CartsController::STORE_NAME), $cart->toArray())
            ->assertCreated()
            ->assertJson(
                [
                    Cart::ID => 1,
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
            ->get(route(CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function ownerCanAccessCart(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::OWNER))
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

        $this->actingAs($salesRep)
            ->get(route(CartsController::SHOW_NAME, $cart))
            ->assertOk()
            ->assertSee(htmlspecialchars($cart->client->company_name, ENT_QUOTES | ENT_HTML401));
    }

    /**
     * @test
     */
    public function salesRepCanDestroyCart(): void
    {
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $salesRep->carts()->save($cart);

        $this->actingAs($salesRep)
            ->delete(route(CartsController::DESTROY_NAME, $cart))
            ->assertOk();
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
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();
        $salesRep->carts()->save($cart);
        $this->actingAs($salesRep)
            ->patch(
                route(CartsController::UPDATE_NAME, $cart),
                [Cart::STATUS => Cart::STATUS_VOID]
            )->assertOk()
            ->assertJson(
                [
                    Cart::ID => $cart->id,
                    Cart::STATUS => Cart::STATUS_VOID,

                ]
            );
    }

}
