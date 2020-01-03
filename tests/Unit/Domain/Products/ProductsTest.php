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
        factory(Product::class)->create();
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
        factory(Product::class)->create();
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
        $product = factory(Product::class)->create();
        $update = factory(Product::class)->make();
        $productUpdateObject = ProductUpdateObject::fromRequest(
            [
                'type' => $update->type,
                'manufacturer' => $update->manufacturer,
                'model' => $update->model,
                'values' => $update->values,
            ]
        );
        ProductUpdateAction::execute($product->id, $productUpdateObject);
        $this->assertDatabaseHas(Product::TABLE, [
            Product::ID => $product->id,
            Product::MODEL => $productUpdateObject['model'],
            Product::VALUES => $update->values,
        ]);

    }
}
