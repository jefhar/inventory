<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Domain\WorkOrders\Actions;

use App\Admin\DataTransferObjects\ClientObject;
use App\Admin\DataTransferObjects\PersonObject;
use App\Admin\Permissions\UserPermissions;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class WorkOrdersStoreAction
{

    /**
     * @param ClientObject $clientObject
     * @param PersonObject|null $personObject
     * @return Client|Builder
     */
    public static function execute(ClientObject $clientObject, ?PersonObject $personObject = null)
    {
        if ($personObject === null && Auth::user()->hasPermissionTo(UserPermissions::WORK_ORDER_OPTIONAL_PERSON)) {
            return Client::firstOrCreate([Client::COMPANY_NAME => $clientObject->company_name]);
        }

        if ($personObject === null) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY]);
        }

        /** @var Client $client */
        $client = Client::firstOrCreate(
            [
                Client::COMPANY_NAME => $clientObject->company_name,
            ]
        );
        $person = new Person(
            [
                Person::FIRST_NAME => $personObject->first_name,
                Person::LAST_NAME => $personObject->last_name,
                Person::EMAIL => $personObject->email,
                Person::PHONE_NUMBER => $personObject->phone_number,
            ]
        );
        $client->person()->save($person);

        return $client;
    }
}
