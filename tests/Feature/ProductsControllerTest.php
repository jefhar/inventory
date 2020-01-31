<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\Products\Controllers\ProductsController;
use App\User;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class ProductsControllerTest
 *
 * @package Tests\Feature
 */
class ProductsControllerTest extends TestCase
{
    use FullObjects;

    private User $user;

    /**
     * @test
     */
    public function storeProductReturnsProduct(): void
    {
        $faker = Factory::create();
        $manufacturer = $faker->company;
        $model = $faker->title;
        $type = factory(Type::class)->create();
        $workOrder = factory(WorkOrder::class)->create();
        $formRequest = [
            'manufacturer' => $manufacturer,
            'model' => $model,
            'radio-group-1575689472139' => 'option-3',
            'select-1575689474390' => 'option-2',
            'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
            'type' => $type->slug,
            'workOrderId' => $workOrder->luhn,
        ];

        $this->actingAs($this->user)
            ->withoutExceptionHandling()
            ->postJson(route(ProductsController::STORE_NAME), $formRequest)
            ->assertCreated()
            ->assertSee($manufacturer)
            ->assertSee(Product::ID)
            ->assertSee(Product::TYPE_ID)
            ->assertSee(Product::WORK_ORDER_ID);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => 1,
                Product::MODEL => $model,
                Product::TYPE_ID => $type->id,
                Product::WORK_ORDER_ID => $workOrder->id,
                Product::MANUFACTURER_ID => 1,
            ]
        );
        $this->assertDatabaseHas(
            Manufacturer::TABLE,
            [
                Manufacturer::ID => 1,
                Manufacturer::NAME => $manufacturer,
            ]
        );
        /** @var Product $theProduct */
        $theProduct = Product::find(1);
        $this->assertContains('option-3', $theProduct->values);
    }

    /**
     * @test
     */
    public function salesRepCanAddPriceToProduct(): void
    {
        $faker = Factory::create();
        $price = $faker->randomNumber();
        /** @var User $salesRep */
        $salesRep = factory(User::class)->make();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $salesRep->save();
        $product = $this->createFullProduct();
        $this->actingAs($salesRep)->withoutExceptionHandling()
            ->patch(
                route(ProductsController::UPDATE_NAME, $product),
                [Product::PRICE => $price]
            )
            ->assertJson(
                [
                    Product::LUHN => $product->luhn,
                    Product::PRICE => $price,
                ]
            )
            ->assertOk();
    }

    /**
     * @test
     */
    public function technicianCannotAddPriceToProduct(): void
    {
        $faker = Factory::create();
        $price = $faker->randomNumber();
        $product = $this->createFullProduct();
        /** @var User $technician */
        $technician = factory(User::class)->make();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $technician->save();

        $this->actingAs($technician)
            ->patch(
                route(ProductsController::UPDATE_NAME, $product),
                [
                    Product::PRICE => $price,
                ]
            )
            ->assertForbidden();
    }

    public function setUp(): void
    {
        parent::setUp();

        /** @var User $user */
        $user = factory(User::class)->make()
            ->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $user->save();
        $this->user = $user;
    }
}
