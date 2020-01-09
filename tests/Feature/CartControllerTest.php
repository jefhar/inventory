<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\User;
use Tests\TestCase;

/**
 * Class CartControllerTest
 *
 * @package Tests\Feature
 */
class CartControllerTest extends TestCase
{
    private User $employee;
    private User $owner;
    private User $salesRep;
    private User $technician;

    /**
     * @test
     */
    public function salesRepCanCreateCart(): void
    {
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $this->actingAs($this->salesRep)
            ->post(
                route(\App\Carts\CartsController::STORE_NAME),
                $cart
            )
            ->assertCreated()
            ->assertJson(
                [
                    \Domain\Carts\Models\Cart::ID => 1,
                ]
            );
    }

    /**
     * @test
     */
    public function technicianCantCreateCart(): void
    {
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $this->actingAs($this->technician)
            ->post(
                route(\App\Carts\CartsController::STORE_NAME),
                $cart
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function salesRepCanAccessCart(): void
    {
        $this->actingAs($this->salesRep)
            ->get(route(\App\Carts\CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function ownerCanAccessCart(): void
    {
        $this->actingAs($this->owner)
            ->get(route(\App\Carts\CartsController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function othersCantAccessCart(): void
    {
        $this->actingAs($this->employee)
            ->get(route(\App\Carts\CartsController::INDEX_NAME))
            ->assertRedirect();

        $this->actingAs($this->technician)
            ->get(route(\App\Carts\CartsController::INDEX_NAME))
            ->assertRedirect();
    }

    /**
     * @test
     */
    public function salesRepCanSeeCart(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $this->salesRep->carts()->save($cart);

        $this->actingAs($this->salesRep)
            ->get(route(\App\Carts\CartsController::SHOW_NAME, $cart))
            ->assertOk()
            ->assertSee($cart->client->company_name);
    }

    /**
     * @test
     */
    public function salesRepCanDestroyCart(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();

        $this->salesRep->carts()->save($cart);

        $this->actingAs($this->salesRep)
            ->get(route(\App\Carts\CartsController::DESTROY_NAME, $cart))
            ->assertOk();
    }

    /**
     * @test
     */
    public function othersCantDestroyCart(): void
    {
        /** @var \Domain\Carts\Models\Cart $employeeCart */
        $employeeCart = factory(\Domain\Carts\Models\Cart::class)->make();

        $this->employee->carts()->save($employeeCart);

        $this->actingAs($this->employee)
            ->get(route(\App\Carts\CartsController::DESTROY_NAME, $employeeCart))
            ->assertRedirect();

        /** @var \Domain\Carts\Models\Cart $employeeCart */
        $technicianCart = factory(\Domain\Carts\Models\Cart::class)->make();

        $this->technician->carts()->save($technicianCart);

        $this->actingAs($this->technician)
            ->get(route(\App\Carts\CartsController::DESTROY_NAME, $employeeCart))
            ->assertRedirect();
    }

    /**
     * @test
     */
    public function salesRepCanUpdateCartStatus(): void
    {
        /** @var \Domain\Carts\Models\Cart $cart */
        $cart = factory(\Domain\Carts\Models\Cart::class)->make();
        $this->salesRep->carts()->save($cart);
        $this->actingAs($this->salesRep)
            ->patch(
                route(\App\Carts\CartsController::UPDATE_NAME, $cart),
                [\Domain\Carts\Models\Cart::STATUS => \Domain\Carts\Models\Cart::STATUS_VOID]
            )->assertOk()
            ->assertJson(
                [
                    \Domain\Carts\Models\Cart::ID => $cart->id,
                    \Domain\Carts\Models\Cart::STATUS => \Domain\Carts\Models\Cart::STATUS_VOID,

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
