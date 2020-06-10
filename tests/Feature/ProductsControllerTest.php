<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Products\Controllers\ProductsController;
use App\Products\Requests\ProductStoreRequest;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\Models\WorkOrder;
use Exception;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @test
     * @throws \JsonException
     */
    public function storeProductReturnsProduct(): void
    {
        $faker = Factory::create();
        $manufacturerName = $faker->company;
        $model = $faker->title;
        /** @var Type $type */
        $type = factory(Type::class)->create();
        /** @var WorkOrder $workOrder */
        $workOrder = $this->createFullWorkOrder();
        $formRequest = [
            ProductStoreRequest::MANUFACTURER_NAME => $manufacturerName,
            ProductStoreRequest::MODEL => $model,
            'radio-group-1575689472139' => 'option-3',
            'select-1575689474390' => 'option-2',
            'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
            ProductStoreRequest::TYPE => $type->slug,
            ProductStoreRequest::WORK_ORDER_ID => $workOrder->luhn,
        ];

        $this->actingAs($this->createEmployee())
            ->withoutExceptionHandling()
            ->postJson(route(ProductsController::STORE_NAME), $formRequest)
            ->assertCreated()
            ->assertSee($manufacturerName)
            ->assertSee(Product::ID)
            ->assertSee(Product::TYPE)
            ->assertSee(Product::WORK_ORDER_ID);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::MODEL => $model,
                Product::TYPE_ID => $type->id,
                Product::WORK_ORDER_ID => $workOrder->id,
            ]
        );
        $this->assertDatabaseHas(
            Manufacturer::TABLE,
            [
                Manufacturer::NAME => $manufacturerName,
            ]
        );
        /** @var Product $theProduct */
        $theProduct = Product::where(
            [
                [Product::MODEL, $model],
                [Product::TYPE_ID, $type->id],
                [Product::WORK_ORDER_ID, $workOrder->id],
            ]
        )->first();
        $this->assertStringContainsString(
            'option-3',
            json_encode($theProduct->values, JSON_THROW_ON_ERROR),
            '`option-3` not found in $product->values array.'
        );
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
                    Product::ID => $product->luhn,
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
