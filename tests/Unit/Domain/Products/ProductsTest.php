<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\Products;

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
}
