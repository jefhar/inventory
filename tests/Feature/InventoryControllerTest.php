<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Products\Controllers\InventoryController;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class InventoryControllerTest
 *
 * @package Tests\Feature
 */
class InventoryControllerTest extends TestCase
{
    use FullUsers;
    use FullObjects;

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
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $this->withoutMix()
            ->actingAs($this->createEmployee())
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSeeText('"className":"form-control-plaintext"', false);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('"className":"form-control-plaintext"', false);
    }

    /**
     * @test
     */
    public function salesRepsSeeAddToCartButton(): void
    {
        $this->withoutMix();
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('Add To Cart &hellip;', false);

        $this->actingAs($this->createEmployee(UserRoles::OWNER))
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee('Add To Cart &hellip;', false);
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

    /**
     * @test
     */
    public function salesRepsSeeTheirExistingCartsOnInventoryPage(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $workOrder = factory(WorkOrder::class)->create();
        $carts = [];
        // Whip up around 20 carts to make sure they all appear
        for ($i = 0; $i < 19; $i++) {
            $product = factory(Product::class)->make();
            $workOrder->products()->save($product);
            $cart = $this->makeFullCart();
            $salesRep->carts()->save($cart);
            $carts[] = $cart;
        }

        // Make one more outside the loop so `$product`  will be defined for phpstan
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $carts[] = $cart;

        $this->actingAs($salesRep)
            ->withoutMix()
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee($carts[0]->client->company_name)
            ->assertSee($carts[1]->client->company_name)
            ->assertSee($carts[2]->client->company_name)
            ->assertSee($carts[3]->client->company_name)
            ->assertSee($carts[4]->client->company_name)
            ->assertSee($carts[5]->client->company_name)
            ->assertSee($carts[6]->client->company_name)
            ->assertSee($carts[7]->client->company_name)
            ->assertSee($carts[8]->client->company_name)
            ->assertSee($carts[9]->client->company_name)
            ->assertSee($carts[10]->client->company_name);

        $otherSalesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($otherSalesRep)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee($carts[0]->client->company_name)
            ->assertDontSee($carts[1]->client->company_name)
            ->assertDontSee($carts[2]->client->company_name)
            ->assertDontSee($carts[3]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name)
            ->assertDontSee($carts[5]->client->company_name)
            ->assertDontSee($carts[6]->client->company_name)
            ->assertDontSee($carts[7]->client->company_name)
            ->assertDontSee($carts[8]->client->company_name)
            ->assertDontSee($carts[9]->client->company_name)
            ->assertDontSee($carts[10]->client->company_name);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function salesRepsDoNotSeeInvoicedCartsOnInventoryPage(): void
    {
        Mail::fake();

        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $carts = [];
        // Whip up around 20 carts to make sure they all appear
        for ($i = 0; $i < 19; $i++) {
            $product = factory(Product::class)->make();
            $workOrder->products()->save($product);
            $cart = $this->makeFullCart();
            $salesRep->carts()->save($cart);
            $carts[] = $cart;
        }

        // Make one more outside the loop so `$product`  will be defined for phpstan
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $carts[] = $cart;

        // Update a cart to be invoiced
        CartPatchAction::execute(
            $carts[4],
            CartPatchObject::fromRequest(
                [
                    Cart::STATUS => Cart::STATUS_INVOICED,
                ]
            )
        );

        $this
            ->withoutMix()
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee($carts[0]->client->company_name)
            ->assertSee($carts[1]->client->company_name)
            ->assertSee($carts[2]->client->company_name)
            ->assertSee($carts[3]->client->company_name)
            ->assertSee($carts[5]->client->company_name)
            ->assertSee($carts[6]->client->company_name)
            ->assertSee($carts[7]->client->company_name)
            ->assertSee($carts[8]->client->company_name)
            ->assertSee($carts[9]->client->company_name)
            ->assertSee($carts[10]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name);

        $otherSalesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($otherSalesRep)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee($carts[0]->client->company_name)
            ->assertDontSee($carts[1]->client->company_name)
            ->assertDontSee($carts[2]->client->company_name)
            ->assertDontSee($carts[3]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name)
            ->assertDontSee($carts[5]->client->company_name)
            ->assertDontSee($carts[6]->client->company_name)
            ->assertDontSee($carts[7]->client->company_name)
            ->assertDontSee($carts[8]->client->company_name)
            ->assertDontSee($carts[9]->client->company_name)
            ->assertDontSee($carts[10]->client->company_name);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function salesRepsDoNotSeeVoidedCartsOnInventoryPage(): void
    {
        Mail::fake();

        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($salesRep);
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $carts = [];
        // Whip up around 20 carts to make sure they all appear
        for ($i = 0; $i < 19; $i++) {
            $product = factory(Product::class)->make();
            $workOrder->products()->save($product);
            $cart = $this->makeFullCart();
            $salesRep->carts()->save($cart);
            $carts[] = $cart;
        }

        // Make one more outside the loop so `$product`  will be defined for phpstan
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $carts[] = $cart;

        // Update a cart to be invoiced
        CartPatchAction::execute(
            $carts[4],
            CartPatchObject::fromRequest(
                [
                    Cart::STATUS => Cart::STATUS_VOID,
                ]
            )
        );

        $this
            ->withoutMix()
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee($carts[0]->client->company_name)
            ->assertSee($carts[1]->client->company_name)
            ->assertSee($carts[2]->client->company_name)
            ->assertSee($carts[3]->client->company_name)
            ->assertSee($carts[5]->client->company_name)
            ->assertSee($carts[6]->client->company_name)
            ->assertSee($carts[7]->client->company_name)
            ->assertSee($carts[8]->client->company_name)
            ->assertSee($carts[9]->client->company_name)
            ->assertSee($carts[10]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name);

        $otherSalesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this->actingAs($otherSalesRep)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee($carts[0]->client->company_name)
            ->assertDontSee($carts[1]->client->company_name)
            ->assertDontSee($carts[2]->client->company_name)
            ->assertDontSee($carts[3]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name)
            ->assertDontSee($carts[5]->client->company_name)
            ->assertDontSee($carts[6]->client->company_name)
            ->assertDontSee($carts[7]->client->company_name)
            ->assertDontSee($carts[8]->client->company_name)
            ->assertDontSee($carts[9]->client->company_name)
            ->assertDontSee($carts[10]->client->company_name);
    }

    /**
     * @test
     */
    public function nonSalesRepsDoNotSeeExistingCartsOnInventoryPage(): void
    {
        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $workOrder = factory(WorkOrder::class)->create();
        $carts = [];
        // Whip up around 20 carts to make sure they all appear
        for ($i = 0; $i < 19; $i++) {
            $product = factory(Product::class)->make();
            $workOrder->products()->save($product);
            $cart = $this->makeFullCart();
            $salesRep->carts()->save($cart);
            $carts[] = $cart;
        }

        // Make one more outside the loop so `$product`  will be defined for phpstan
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $cart = $this->makeFullCart();
        $salesRep->carts()->save($cart);
        $carts[] = $cart;
        $technician = $this->createEmployee(UserRoles::TECHNICIAN);
        $this->actingAs($technician)
            ->withoutMix()
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee($carts[0]->client->company_name)
            ->assertDontSee($carts[1]->client->company_name)
            ->assertDontSee($carts[2]->client->company_name)
            ->assertDontSee($carts[3]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name)
            ->assertDontSee($carts[5]->client->company_name)
            ->assertDontSee($carts[6]->client->company_name)
            ->assertDontSee($carts[7]->client->company_name)
            ->assertDontSee($carts[8]->client->company_name)
            ->assertDontSee($carts[9]->client->company_name)
            ->assertDontSee($carts[10]->client->company_name);
        $employee = $this->createEmployee();
        $this->actingAs($employee)
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertDontSee($carts[0]->client->company_name)
            ->assertDontSee($carts[1]->client->company_name)
            ->assertDontSee($carts[2]->client->company_name)
            ->assertDontSee($carts[3]->client->company_name)
            ->assertDontSee($carts[4]->client->company_name)
            ->assertDontSee($carts[5]->client->company_name)
            ->assertDontSee($carts[6]->client->company_name)
            ->assertDontSee($carts[7]->client->company_name)
            ->assertDontSee($carts[8]->client->company_name)
            ->assertDontSee($carts[9]->client->company_name)
            ->assertDontSee($carts[10]->client->company_name);
    }

    /**
     * @test
     */
    public function ownerSeesAllExistingCartsOnInventoryPage(): void
    {
        $salesRep1 = $this->createEmployee(UserRoles::SALES_REP);
        $salesRep2 = $this->createEmployee(UserRoles::SALES_REP);
        $workOrder = factory(WorkOrder::class)->create();
        $carts = [];
        // Whip up around 20 carts to make sure they all appear
        for ($i = 0; $i < 10; $i++) {
            $product = factory(Product::class)->make();
            $workOrder->products()->save($product);
            $cart = $this->makeFullCart();
            $salesRep1->carts()->save($cart);
            $carts[] = $cart;
        }

        // Make one more outside the loop so `$product`  will be defined for phpstan
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $cart = $this->makeFullCart();
        $salesRep1->carts()->save($cart);
        $carts[] = $cart;
        for ($i = 0; $i < 10; $i++) {
            $product = factory(Product::class)->make();
            $workOrder->products()->save($product);
            $cart = $this->makeFullCart();
            $salesRep2->carts()->save($cart);
            $carts[] = $cart;
        }

        $owner = $this->createEmployee(UserRoles::OWNER);
        $this->actingAs($owner)
            ->withoutMix()
            ->get(route(InventoryController::SHOW_NAME, $product))
            ->assertSee($carts[0]->client->company_name)
            ->assertSee($carts[1]->client->company_name)
            ->assertSee($carts[2]->client->company_name)
            ->assertSee($carts[3]->client->company_name)
            ->assertSee($carts[4]->client->company_name)
            ->assertSee($carts[5]->client->company_name)
            ->assertSee($carts[14]->client->company_name)
            ->assertSee($carts[15]->client->company_name)
            ->assertSee($carts[16]->client->company_name)
            ->assertSee($carts[17]->client->company_name)
            ->assertSee($carts[18]->client->company_name)
            ->assertSee($carts[19]->client->company_name);
    }
}
