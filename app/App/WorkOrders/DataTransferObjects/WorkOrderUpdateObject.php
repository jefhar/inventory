<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class WorkOrderUpdateObject
 *
 * @package App\WorkOrders\DataTransferObjects
 */
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
                WorkOrder::INTAKE => $validated[WorkOrder::INTAKE] ?? null,
                Client::COMPANY_NAME => $validated[Client::COMPANY_NAME] ?? null,
                Person::FIRST_NAME => $validated[Person::FIRST_NAME] ?? null,
                Person::LAST_NAME => $validated[Person::LAST_NAME] ?? null,
                Person::PHONE_NUMBER => $validated[Person::PHONE_NUMBER] ?? null,
                Person::EMAIL => $validated[Person::EMAIL] ?? null,
            ]
        );
    }
}
