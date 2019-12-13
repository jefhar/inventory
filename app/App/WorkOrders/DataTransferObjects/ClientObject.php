<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Domain\WorkOrders\Models\Client;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class ClientObject
 *
 * @package App\WorkOrders\DataTransferObjects
 */
class ClientObject extends DataTransferObject
{
    public string $company_name;

    /**
     * @param array $validated
     * @return static
     */
    public static function fromRequest(array $validated): self
    {
        return new self(
            [
                Client::COMPANY_NAME => $validated[Client::COMPANY_NAME],
            ]
        );
    }
}
