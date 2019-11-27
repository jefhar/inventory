<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\DataTransferObjects\WorkOrderUpdateObject;
use App\Admin\Permissions\UserPermissions;
use App\User;
use Domain\WorkOrders\Actions\WorkOrdersUpdateAction;
use Domain\WorkOrders\WorkOrder;
use Tests\TestCase;

class WorkOrderUpdateActionTest extends TestCase
{
    private User $user;

    /**
     * @test
     */
    public function togglesIsLocked(): void
    {
        $workOrder = factory(WorkOrder::class)->make();
        $workOrder->is_locked = false;
        $workOrder->save();
        $workOrderUpdateObjectLocked = WorkOrderUpdateObject::fromRequest(
            [
                WorkOrder::IS_LOCKED => true,
            ]
        );
        $workOrderUpdateObjectUnLocked = WorkOrderUpdateObject::fromRequest(
            [
                WorkOrder::IS_LOCKED => false,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObjectLocked);
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => true,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObjectUnLocked);
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => false,
            ]
        );
    }

    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $this->user = $user;
    }
}
