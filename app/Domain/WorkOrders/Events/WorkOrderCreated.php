<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Events;

use Domain\WorkOrders\WorkOrder;
use Illuminate\Queue\SerializesModels;

class WorkOrderCreated
{
    use SerializesModels;

    public WorkOrder $workOrder;

    public function __construct(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder;
    }
}
