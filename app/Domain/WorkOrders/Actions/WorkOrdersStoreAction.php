<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Actions;

use App\Admin\DataTransferObjects\ClientObject;
use App\Admin\DataTransferObjects\PersonObject;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
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
     * @param PersonObject|null $personObject
     * @return WorkOrder
     */
    public static function execute(ClientObject $clientObject, ?PersonObject $personObject = null): WorkOrder
    {
        /** @var Client $client */
        $client = Client::firstOrCreate([Client::COMPANY_NAME => $clientObject->company_name]);

        if ($personObject !== null) {
            $client->person()->create(
                [
                    Person::FIRST_NAME => $personObject->first_name,
                    Person::LAST_NAME => $personObject->last_name,
                    Person::EMAIL => $personObject->email,
                    Person::PHONE_NUMBER => $personObject->phone_number,
                ]
            );
        }
        $workOrder = new WorkOrder();
        $workOrder->client()->associate($client);
        $workOrder->user()->associate(Auth::user());
        $workOrder->save();

        return $workOrder;
    }
}
