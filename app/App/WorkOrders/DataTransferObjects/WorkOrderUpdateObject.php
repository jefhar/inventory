<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Spatie\DataTransferObject\DataTransferObject;

class WorkOrderUpdateObject extends DataTransferObject
{
    public ?bool $is_locked = null;
    public ?string $intake = null;
    public ?string $company_name = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $phone_number = null;
    public ?string $email = null;

    /**
     * @param array $validated
     * @return WorkOrderUpdateObject
     */
    public static function fromRequest(array $validated): WorkOrderUpdateObject
    {
        return new self(
            [
                WorkOrder::IS_LOCKED => $validated[WorkOrder::IS_LOCKED] ?? null,
                WorkOrder::INTAKE => $validated[WorkOrder::INTAKE] ?? '',
                Client::COMPANY_NAME => $validated[Client::COMPANY_NAME] ?? '',
                Person::FIRST_NAME => $validated[Person::FIRST_NAME] ?? '',
                Person::LAST_NAME => $validated[Person::LAST_NAME] ?? '',
                Person::PHONE_NUMBER => $validated[Person::PHONE_NUMBER] ?? '',
                Person::EMAIL => $validated[Person::EMAIL] ?? '',
            ]
        );
    }
}
