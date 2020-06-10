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

class WorkOrderUpdateResource
{
    private const LUHN = 'luhn';
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const CLIENT_FIRST_NAME = 'first_name';
    public const CLIENT_LAST_NAME = 'last_name';
    public const CLIENT_PHONE_NUMBER = 'phone_number';
    public const EMAIL = 'email';
    public const ID = 'id';
    public const INTAKE = 'intake';
    public const IS_LOCKED = 'is_locked';

    public function toArray($request): array
    {
        $translation = [];

        if (filled($request[Client::COMPANY_NAME] ?? null)) {
            $translation[self::CLIENT_COMPANY_NAME] = $request[Client::COMPANY_NAME];
        }

        if (filled($request[Person::EMAIL] ?? null)) {
            $translation[self::EMAIL] = $request[Person::EMAIL];
        }

        if (filled($request[Person::FIRST_NAME] ?? null)) {
            $translation[self::CLIENT_FIRST_NAME] = $request[Person::FIRST_NAME];
        }

        if (filled($request[WorkOrder::INTAKE] ?? null)) {
            $translation[self::INTAKE] = $request[WorkOrder::INTAKE];
        }

        if (filled($request[WorkOrder::IS_LOCKED] ?? null)) {
            $translation[self::IS_LOCKED] = $request[WorkOrder::IS_LOCKED];
        }

        if (filled($request[WorkOrder::LUHN] ?? null)) {
            $translation[self::ID] = $request[WorkOrder::LUHN];
        }

        if (filled($request[Person::PHONE_NUMBER] ?? null)) {
            $translation[self::CLIENT_PHONE_NUMBER] = $request[Person::PHONE_NUMBER];
        }

        if (filled($request[Person::LAST_NAME] ?? null)) {
            $translation[self::CLIENT_LAST_NAME] = $request[Person::LAST_NAME];
        }

        return $translation;
    }
}
