<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Resources;

use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkOrderResource extends JsonResource
{
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const EMAIL = 'email';
    public const CLIENT_FIRST_NAME = 'first_name';
    public const ID = 'id';
    public const INTAKE = 'intake';
    public const IS_LOCKED = 'is_locked';
    public const CLIENT_LAST_NAME = 'last_name';
    public const CLIENT_PHONE_NUMBER = 'phone_number';
    private const LUHN = 'luhn';

    public function toArray($request)
    {
        $translation = [
            self::CLIENT_COMPANY_NAME => $request[Client::COMPANY_NAME],
            self::EMAIL => $request[Person::EMAIL],
            self::CLIENT_FIRST_NAME => $request[Person::FIRST_NAME],
            self::INTAKE => $request[WorkOrder::INTAKE],
            self::IS_LOCKED => $request[WorkOrder::IS_LOCKED],
            self::ID => $request[WorkOrder::LUHN],
            self::CLIENT_PHONE_NUMBER => $request[Person::PHONE_NUMBER],
            self::CLIENT_LAST_NAME => $request[Person::LAST_NAME],
        ];

        return $translation;
    }
}
