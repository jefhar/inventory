<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Products\Controllers\InventoryController;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\WorkOrder;
use Tests\TestCase;
use Tests\Traits\FullUsers;

/**
 * Class InventoryControllerTest
 *
 * @package Tests\Feature
 */
class InventoryControllerTest extends TestCase
{
    use FullUsers;

    /**
     * @test
     */
    public function inventoryLinkOnNavBar(): void
    {
        $this->actingAs($this->createEmployee())
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
        $this->withoutMix()
            ->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->get(route(InventoryController::INDEX_NAME))
            ->assertOk()
            ->assertSee('No Products Available For Sale.');

        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $this->actingAs($this->createEmployee())
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
        $this->withoutMix()
            ->actingAs($this->createEmployee())
            ->get(route(InventoryController::SHOW_NAME, $product))
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
        $this->patch(route(InventoryController::UPDATE_NAME, $product), [Product::MODEL => $update->model,])
            ->assertRedirect();
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->patch(route(InventoryController::UPDATE_NAME, $product), [Product::MODEL => $update->model,])
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
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(InventoryController::UPDATE_NAME, $product),
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
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('"className":"form-control-plaintext"');
        $this->actingAs($this->createEmployee(UserRoles::OWNER))
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

        $this->withoutMix()
            ->actingAs($this->createEmployee())
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('"className":"form-control-plaintext"');

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('"className":"form-control-plaintext"');
    }

    /**
     * @test
     */
    public function salesRepsSeeAddToCartButton(): void
    {
        $this->withoutMix();
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('Add To Cart &hellip;');

        $this->actingAs($this->createEmployee(UserRoles::OWNER))
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

        $this->actingAs($this->createEmployee(UserRoles::EMPLOYEE))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('Add To Cart &hellip;');

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee('Add To Cart &hellip;');
    }
}
