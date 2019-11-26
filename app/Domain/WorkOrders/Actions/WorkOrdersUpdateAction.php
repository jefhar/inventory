<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Actions;

use Domain\WorkOrders\WorkOrder;

class WorkOrdersUpdateAction
{

    public static function execute(WorkOrder $workOrder, array $array): WorkOrder
    {
        $workOrder->is_locked = $array[WorkOrder::IS_LOCKED];
        $workOrder->save();

        return $workOrder;
    }
}
