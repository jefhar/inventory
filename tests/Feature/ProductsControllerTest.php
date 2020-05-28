<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Products\Controllers\ProductsController;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\Models\WorkOrder;
use Exception;
use Faker\Factory;
use Support\Requests\ProductStore;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\FullObjects;
use Tests\Traits\FullUsers;

/**
 * Class ProductsControllerTest
 *
 * @package Tests\Feature
 */
class ProductsControllerTest extends TestCase
{
    use FullObjects;
    use FullUsers;

    /**
     * @test
     */
    public function storeProductReturnsProduct(): void
    {
        $faker = Factory::create();
        $manufacturer = $faker->company;
        $model = $faker->title;
        /** @var Type $type */
        $type = factory(Type::class)->create();
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $formRequest = [
            ProductStore::MANUFACTURER_NAME,
            ProductStore::MODEL => $model,
            'radio-group-1575689472139' => 'option-3',
            'select-1575689474390' => 'option-2',
            'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
            ProductStore::TYPE => $type->slug,
            ProductStore::WORK_ORDER_ID => $workOrder->luhn,
        ];

        $this->actingAs($this->createEmployee())
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
        $this->assertContains('option-3', $theProduct->values, '`option-3` not found in $product->values array.');
    }

    /**
     * @test
     * @throws Exception
     */
    public function salesRepCanAddPriceToProduct(): void
    {
        $price = random_int(100, PHP_INT_MAX) / 100;
        $product = $this->createFullProduct();
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->patch(route(ProductsController::UPDATE_NAME, $product), [Product::PRICE => $price])
            ->assertJson(
                [
                    Product::ID => $product->id,
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
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(route(ProductsController::UPDATE_NAME, $product), [Product::PRICE => $price,])
            ->assertForbidden();
    }

    /**
     * @test
     * @throws Exception
     */
    public function negativePriceResultsInUnprocessableEntity(): void
    {
        $product = $this->createFullProduct();
        $price = random_int(PHP_INT_MIN, 0) / 100;
        $this->actingAs($this->createEmployee(UserRoles::SALES_REP))
            ->patch(route(ProductsController::UPDATE_NAME, $product), [Product::PRICE => $price,])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
