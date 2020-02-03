<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\Controllers\CartsController;
use App\User;
use Domain\Carts\Models\Cart;
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

    private User $employee;
    private User $owner;
    private User $salesRep;
    private User $technician;

    /**
     * @test
     */
    public function salesRepCanCreateCart(): void
    {
        $cart = factory(Cart::class)->make();
        $this->actingAs($this->salesRep)
            ->withoutExceptionHandling()
            ->post(
                route(CartsController::STORE_NAME),
                $cart->toArray()
            )
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
        $this->actingAs($this->technician)
            ->post(
                route(CartsController::STORE_NAME),
                $cart->toArray()
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanAccessCart(): void
    {
        $this->actingAs($this->salesRep)
            ->get(route(CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function ownerCanAccessCart(): void
    {
        $this->actingAs($this->owner)
            ->get(route(CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function othersCantAccessCart(): void
    {
        $this->actingAs($this->employee)
            ->get(route(CartsController::INDEX_NAME))
            ->assertForbidden();

        $this->actingAs($this->technician)
            ->get(route(CartsController::INDEX_NAME))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanSeeCart(): void
    {
        $cart = $this->makeFullCart();
        $this->salesRep->carts()->save($cart);

        $this->actingAs($this->salesRep)
            ->get(route(CartsController::SHOW_NAME, $cart))
            ->assertOk()
            ->assertSee($cart->client->company_name);
    }

    /**
     * @test
     */
    public function salesRepCanDestroyCart(): void
    {
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();

        $this->salesRep->carts()->save($cart);

        $this->actingAs($this->salesRep)
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

        $this->employee->carts()->save($employeeCart);

        $this->actingAs($this->employee)
            ->delete(route(CartsController::DESTROY_NAME, $employeeCart))
            ->assertForbidden();

        /** @var Cart $employeeCart */
        $technicianCart = factory(Cart::class)->make();

        $this->technician->carts()->save($technicianCart);

        $this->actingAs($this->technician)
            ->delete(route(CartsController::DESTROY_NAME, $employeeCart))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanUpdateCartStatus(): void
    {
        /** @var Cart $cart */
        $cart = factory(Cart::class)->make();
        $this->salesRep->carts()->save($cart);
        $this->actingAs($this->salesRep)
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

    public function setUp(): void
    {
        parent::setUp();
        /** @var User $salesRep */
        $salesRep = factory(User::class)->make();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $salesRep->save();
        $this->salesRep = $salesRep;

        /** @var User $owner */
        $owner = factory(User::class)->make();
        $owner->assignRole(UserRoles::OWNER);
        $owner->save();
        $this->owner = $owner;

        /** @var User $technician */
        $technician = factory(User::class)->make();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->save();
        $this->technician = $technician;

        /** @var User $employee */
        $employee = factory(User::class)->make();
        $employee->assignRole(UserRoles::EMPLOYEE);
        $employee->save();
        $this->employee = $employee;
    }
}
