<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\Products;

use App\Products\DataTransferObject\ProductUpdateObject;
use Domain\Products\Actions\ProductUpdateAction;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Tests\TestCase;

/**
 * Class ProductsTest
 *
 * @package Tests\Unit
 */
class ProductsTest extends TestCase
{

    /**
     * @test
     */
    public function productHasManufacturerAndModel(): void
    {
        $faker = Factory::create();
        $manufacturerName = $faker->company;
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $manufacturerName]);
        $product->manufacturer()->associate($manufacturer);
        $product->workOrder()->associate($workOrder);
        $product->save();
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::MANUFACTURER_ID => $manufacturer->id,
            ]
        );
        $this->assertDatabaseHas(
            Manufacturer::TABLE,
            [
                Manufacturer::ID => $manufacturer->id,
                Manufacturer::NAME => $manufacturer->name,
            ]
        );
        $product->fresh();
        $this->assertEquals($manufacturerName, $product->manufacturer->name);
    }

    /**
     * @test
     */
    public function createdProductHasLuhn(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);

        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => 1,
                Product::LUHN => 18,
            ]
        );
    }

    /**
     * @test
     */
    public function createdProductIsAvailableForSale(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => 1,
                Product::STATUS => Product::STATUS_AVAILABLE,
            ]
        );
    }

    /**
     * @test
     */
    public function updateProductUpdatesProduct(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make();
        $workOrder->products()->save($product);
        $update = factory(Product::class)->make();
        $productUpdateObject = ProductUpdateObject::fromRequest(
            [
                'type' => $update->type->slug,
                'manufacturer' => $update->manufacturer->name,
                'model' => $update->model,
                'values' => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
                ],
            ]
        );
        ProductUpdateAction::execute($product, $productUpdateObject);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::MODEL => $productUpdateObject->model,
                Product::VALUES => json_encode(
                    [
                        'radio-group-1575689472139' => 'option-3',
                        'select-1575689474390' => 'option-2',
                        'textarea-1575689477555' => 'textarea text. Bwahahahahaaaa',
                    ],
                    JSON_THROW_ON_ERROR,
                    512
                ),
            ]
        );
    }

    /**
     * @test
     */
    public function updateProductCreatesSerialField(): void
    {
        $faker = Factory::create();
        $serial = $faker->isbn13;
        $workOrder = factory(WorkOrder::class)->create();
        $product = factory(Product::class)->make(
            [
                'values' => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'text-1575689474910' => 'option-1',
                ],
            ]
        );

        $workOrder->products()->save($product);
        $this->assertDatabaseMissing(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::SERIAL => $serial,
            ]
        );

        $productUpdateObject = ProductUpdateObject::fromRequest(
            [
                'type' => $product->type->slug,
                'manufacturer' => $product->manufacturer->name,
                'model' => $product->model,
                'values' => [
                    'radio-group-1575689472139' => 'option-3',
                    'select-1575689474390' => 'option-2',
                    'text-1575689474910' => 'option-1',
                    'serial' => $serial,
                ],
            ]
        );
        ProductUpdateAction::execute($product, $productUpdateObject);
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::SERIAL => $serial,
            ]
        );
    }
}
