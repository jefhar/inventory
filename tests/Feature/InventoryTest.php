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
use Tests\TestCase;

/**
 * Class InventoryTest
 *
 * @package Tests\Feature
 */
class InventoryTest extends TestCase
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
            ->assertUnauthorized();
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

        $product = factory(Product::class)->create();
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
        $product = factory(Product::class)->create();
        $this->get(route(InventoryController::SHOW_NAME, $product))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function productPageForUserIsOk(): void
    {
        $product = factory(Product::class)->create();
        $this->get(route(InventoryController::SHOW_NAME, $product))
            ->assertOk()
            ->assertSee($product->model);
    }

    /**
     * @test
     */
    public function updateProductUnauthorizedForNonTechs(): void
    {
        $product = factory(Product::class)->create();
        $update = factory(Product::class)->make();
        $this->patch(
            route(InventoryController::UPDATE_NAME, $product),
            [
                Product::MODEL => $update->model,
            ]
        )
            ->assertUnauthorized();
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $this->actingAs($salesRep)
            ->patch(
                route(InventoryController::UPDATE_NAME, $product),
                [
                    Product::MODEL => $update->model,
                ]
            )
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function updateProductForTechsIsOk(): void
    {
        $product = factory(Product::class)->create();
        $update = factory(Product::class)->make();
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->save();
        $this->actingAs($technician)
            ->patch(
                route(InventoryController::UPDATE_NAME, $product),
                [
                    Product::MODEL => $update->model,
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
}
