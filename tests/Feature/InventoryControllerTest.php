<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\Products\Controllers\InventoryController;
use App\User;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\WorkOrder;
use Tests\TestCase;

/**
 * Class InventoryControllerTest
 *
 * @package Tests\Feature
 */
class InventoryControllerTest extends TestCase
{
    /**
     * @test
     */
    public function inventoryLinkOnNavBar(): void
    {
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $user->save();
        $this->actingAs($user)
            ->get('/home')
            ->assertSeeText('Inventory');
    }

    /**
     * @test
     */
    public function inventoryPageForUnauthorizedIsUnauthorized(): void
    {
        $this->get(route(InventoryController::INDEX_NAME))
            ->assertRedirect();
    }

    /**
     * @test
     */
    public function inventoryPageForAuthorizedUserIsOk(): void
    {
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::SALES_REP);
        $user->save();
        $this->actingAs($user)
            ->get(route(InventoryController::INDEX_NAME))
            ->assertOk()
            ->assertSee('No Products Available For Sale.');

        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $this->actingAs($user)
            ->get(route(InventoryController::INDEX_NAME))
            ->assertOk()
            ->assertSee($product->model);
    }

    /**
     * @test
     */
    public function productPageForUnauthorizedUserIsUnauthorized(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $this->get(route(InventoryController::SHOW_NAME, $product))
            ->assertRedirect();
    }

    /**
     * @test
     */
    public function productPageForUserIsOk(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::EMPLOYEE);
        $user->save();
        $this->actingAs($user)
            ->withoutExceptionHandling()
            ->get(route(\App\Products\Controllers\InventoryController::SHOW_NAME, $product))
            ->assertOk()
            ->assertSee($product->model);
    }

    /**
     * @test
     */
    public function updateProductForbiddenForNonTechs(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $update = factory(Product::class)->make();
        $this->patch(
            route(InventoryController::UPDATE_NAME, $product),
            [
                Product::MODEL => $update->model,
            ]
        )
            ->assertRedirect();
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $this->actingAs($salesRep)
            ->withExceptionHandling()
            ->patch(
                route(InventoryController::UPDATE_NAME, $product),
                [
                    Product::MODEL => $update->model,
                ]
            )
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function updateProductForTechsIsOk(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $update = factory(Product::class)->make();
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->save();
        $this->actingAs($technician)
            ->withoutExceptionHandling()
            ->patch(
                route(\App\Products\Controllers\InventoryController::UPDATE_NAME, $product),
                [
                    'manufacturer' => $update->manufacturer->name,
                    Product::MODEL => $update->model,
                    'type' => $update->type->slug,
                    'values' => [],
                ]
            )
            ->assertOk()
            ->assertJson(
                [
                    Product::MODEL => $update->model,
                ]
            );
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::MODEL => $update->model,
            ]
        );
    }

    /**
     * @test
     */
    public function salesRepsSeeInventoryAsEditable(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $owner = factory(User::class)->create();
        $owner->assignRole(UserRoles::OWNER);
        $this->actingAs($salesRep)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('"className":"form-control-plaintext"');
        $this->actingAs($owner)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('"className":"form-control-plaintext"');
    }

    /**
     * @test
     */
    public function othersSeeInventoryItemAsReadOnly(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $employee = factory(User::class)->create();
        $employee->assignRole(UserRoles::EMPLOYEE);
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $this->actingAs($employee)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('"className":"form-control-plaintext"');
        $this->actingAs($technician)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('"className":"form-control-plaintext"');
    }

    /**
     * @test
     */
    public function salesRepsSeeAddToCartButton(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $this->actingAs($salesRep)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('Add To Cart &hellip;');

        $owner = factory(User::class)->create();
        $owner->assignRole(UserRoles::SALES_REP);
        $this->actingAs($owner)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('Add To Cart &hellip;');
    }

    /**
     * @test
     */
    public function othersDontSeeAddToCartButton(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $employee = factory(User::class)->create();
        $employee->assignRole(UserRoles::EMPLOYEE);
        $this->actingAs($employee)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('Add To Cart &hellip;');

        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $this->actingAs($technician)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('Add To Cart &hellip;');
    }
}
