<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\Models\WorkOrder;
use Tests\TestCase;

/**
 * Class WorkOrdersHaveProductsTest
 *
 * @package Tests\Unit
 */
class WorkOrdersHaveProductsTest extends TestCase
{
    /**
     * @test
     */
    public function workOrderHasManyProducts(): void
    {
        /** @var Product $product1 */
        $product1 = factory(Product::class)->make();
        /** @var Product $product2 */
        $product2 = factory(Product::class)->make();
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $workOrder->products()->save($product1);
        $product2->workOrder()->associate($workOrder);
        $product2->save();
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product1->id,
                Product::WORK_ORDER_ID => $workOrder->id,
            ]
        );
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product2->id,
                Product::WORK_ORDER_ID => $workOrder->id,
            ]
        );
    }

    /**
     * @test
     */
    public function productsHaveAType(): void
    {
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        /** @var Product $product */
        $product = factory(Product::class)->make();

        /** @var Type $type */
        $type = factory(Type::class)->create();
        $product->type()->associate($type);
        $workOrder->products()->save($product);
        $product->save();
        $this->assertDatabaseHas(
            Product::TABLE,
            [
                Product::ID => $product->id,
                Product::TYPE_ID => $type->id,
            ]
        );
    }
}
