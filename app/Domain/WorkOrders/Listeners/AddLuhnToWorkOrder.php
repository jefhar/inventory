<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Listeners;

use Domain\WorkOrders\Events\WorkOrderCreated;
use Tdely\Luhn\Luhn;

class AddLuhnToWorkOrder
{

    public function handle(WorkOrderCreated $event)
    {
        $id = $event->workOrder->id;
        $luhn = Luhn::create($id);
        $event->workOrder->luhn = $luhn;
        $event->workOrder->save();
    }

}
