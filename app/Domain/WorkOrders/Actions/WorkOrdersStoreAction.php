<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Actions;

use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;

/**
 * Class WorkOrdersStoreAction
 *
 * @package Domain\WorkOrders\Actions
 */
class WorkOrdersStoreAction
{

    /**
     * @param ClientObject $clientObject
     * @param PersonObject $personObject
     * @return WorkOrder
     */
    public static function execute(ClientObject $clientObject, PersonObject $personObject): WorkOrder
    {
        $client = Client::firstOrCreate([Client::COMPANY_NAME => $clientObject->company_name]);

        $client->person()->save(
            new Person(
                [
                    Person::FIRST_NAME => $personObject->first_name,
                    Person::LAST_NAME => $personObject->last_name,
                    Person::EMAIL => $personObject->email,
                    Person::PHONE_NUMBER => $personObject->phone_number,
                ]
            )
        );
        $workOrder = new WorkOrder();
        $workOrder->client()->associate($client);
        $workOrder->user()->associate(Auth::user());
        $workOrder->save();

        return $workOrder;
    }
}
