<?php

namespace Tests\Unit;

use Domain\Products\Models\Product;
use Domain\WorkOrders\WorkOrder;
use Tests\TestCase;

class WorkOrdersHaveProductsTest extends TestCase
{
    /**
     * @test
     */
    public function workOrderHasManyProducts(): void
    {
        $product1 = new Product();
        $product2 = new Product();
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
}
