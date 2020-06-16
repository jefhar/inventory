<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Events;

use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Queue\SerializesModels;

/**
 * Class WorkOrderCreated
 *
 * @package Domain\WorkOrders\Events
 */
class WorkOrderCreated
{
    use SerializesModels;

    public WorkOrder $workOrder;

    /**
     * WorkOrderCreated constructor.
     *
     * @param WorkOrder $workOrder
     */
    public function __construct(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
    }
}
