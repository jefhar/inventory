<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Actions;

use App\WorkOrders\DataTransferObjects\WorkOrderUpdateObject;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;

/**
 * Class WorkOrdersUpdateAction
 *
 * @package Domain\WorkOrders\Actions
 */
class WorkOrdersUpdateAction
{

    /**
     * @param WorkOrder $workOrder
     * @param WorkOrderUpdateObject $workOrderUpdateObject
     * @return array
     */
    public static function execute(WorkOrder $workOrder, WorkOrderUpdateObject $workOrderUpdateObject): array
    {
        $changedFields[WorkOrder::ID] = $workOrder->luhn;
        $workOrder->client->loadCount('person');
        $client = $workOrder->client;
        $person = new Person();
        if ($workOrder->client->person_count > '0') {
            $person = $client->person;
        }

        if (isset($workOrderUpdateObject->is_locked)) {
            $workOrder->is_locked = $workOrderUpdateObject->is_locked;
            $changedFields[WorkOrder::IS_LOCKED] = $workOrderUpdateObject->is_locked;
        }
        if (isset($workOrderUpdateObject->intake)) {
            $workOrder->intake = $workOrderUpdateObject->intake;
            $changedFields[WorkOrder::INTAKE] = $workOrderUpdateObject->intake;
        }
        if (isset($workOrderUpdateObject->company_name)) {
            $client->company_name = $workOrderUpdateObject->company_name;
            $changedFields[Client::COMPANY_NAME] = $workOrderUpdateObject->company_name;
        }
        if (isset($workOrderUpdateObject->first_name)) {
            $person->first_name = $workOrderUpdateObject->first_name;
            $changedFields[Person::FIRST_NAME] = $workOrderUpdateObject->first_name;
        }
        if (isset($workOrderUpdateObject->last_name)) {
            $person->last_name = $workOrderUpdateObject->last_name;
            $changedFields[Person::LAST_NAME] = $workOrderUpdateObject->last_name;
        }
        if (isset($workOrderUpdateObject->phone_number)) {
            $person->phone_number = $workOrderUpdateObject->phone_number;
            $changedFields[Person::PHONE_NUMBER] = $workOrderUpdateObject->phone_number;
        }
        if (isset($workOrderUpdateObject->email)) {
            $person->email = $workOrderUpdateObject->email;
            $changedFields[Person::EMAIL] = $workOrderUpdateObject->email;
        }

        $client->person()->save($person);
        $workOrder->client()->associate($client);
        $workOrder->save();
        $client->push();

        return $changedFields;
    }
}
