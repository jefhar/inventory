<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Listeners;

use Domain\WorkOrders\Events\WorkOrderCreated;
use App\Support\Luhn;

/**
 * Class AddLuhnToWorkOrder
 *
 * @package Domain\WorkOrders\Listeners
 */
class AddLuhnToWorkOrder
{
    /**
     * @param WorkOrderCreated $event
     */
    public function handle(WorkOrderCreated $event): void
    {
        $event->workOrder->luhn = Luhn::create($event->workOrder->id);
        $event->workOrder->save();
    }
}
