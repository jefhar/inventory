<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Actions;

use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use App\WorkOrders\DataTransferObjects\WorkOrderObject;
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
     * @param WorkOrderObject $workOrderObject
     * @param ClientObject $clientObject
     * @param PersonObject $personObject
     * @return array
     */
    public static function execute(
        WorkOrder $workOrder,
        WorkOrderObject $workOrderObject,
        ClientObject $clientObject,
        PersonObject $personObject
    ): array {
        $changedFields[WorkOrder::ID] = $workOrder->id;
        $changedFields[WorkOrder::LUHN] = $workOrder->luhn;
        $client = $workOrder->client;
        $person = $client->person;

        // Override only what is in $wOO. Keep original data. Keep track of changes
        if (isset($workOrderObject->is_locked)) {
            $workOrder->is_locked = $workOrderObject->is_locked;
            $changedFields[WorkOrder::IS_LOCKED] = $workOrderObject->is_locked;
        }
        if (isset($workOrderObject->intake)) {
            $workOrder->intake = $workOrderObject->intake;
            $changedFields[WorkOrder::INTAKE] = $workOrderObject->intake;
        }
        if (isset($clientObject->company_name)) {
            $client->company_name = $clientObject->company_name;
            $changedFields[Client::COMPANY_NAME] = $clientObject->company_name;
        }
        if (isset($personObject->first_name)) {
            $person->first_name = $personObject->first_name;
            $changedFields[Person::FIRST_NAME] = $personObject->first_name;
        }
        if (isset($personObject->last_name)) {
            $person->last_name = $personObject->last_name;
            $changedFields[Person::LAST_NAME] = $personObject->last_name;
        }
        if (isset($personObject->phone_number)) {
            $person->phone_number = $personObject->phone_number;
            $changedFields[Person::PHONE_NUMBER] = $personObject->phone_number;
        }
        if (isset($personObject->email)) {
            $person->email = $personObject->email;
            $changedFields[Person::EMAIL] = $personObject->email;
        }

        $client->person()->save($person);
        $client->save();
        $workOrder->client()->associate($client);
        $workOrder->save();

        return $changedFields;
    }
}
