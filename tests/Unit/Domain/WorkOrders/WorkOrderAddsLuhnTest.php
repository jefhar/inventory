<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\WorkOrders;

use Domain\WorkOrders\Events\WorkOrderCreated;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class WorkOrderAddsLuhnTest
 *
 * @package Tests\Unit\Domain\WorkOrders
 */
class WorkOrderAddsLuhnTest extends TestCase
{
    /**
     * @test
     */
    public function createdWorkOrderThrowsEvent(): void
    {
        Event::fake();
        $workOrder = factory(WorkOrder::class)->make();
        $workOrder->save();
        Event::assertDispatched(
            WorkOrderCreated::class,
            function ($e) use ($workOrder) {
                return $e->workOrder->id === $workOrder->id;
            }
        );
    }

    /**
     * @test
     */
    public function workOrderAddsLuhn(): void
    {
        factory(WorkOrder::class)->create();
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => 1,
                WorkOrder::LUHN => 18,
            ]
        );
    }
}
