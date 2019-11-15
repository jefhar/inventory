<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\Admin\DataTransferObjects;

use Domain\WorkOrders\Client;
use Spatie\DataTransferObject\DataTransferObject;

class ClientObject extends DataTransferObject
{
    public string $company_name;

    public static function fromRequest(array $validated): self
    {
        return new self(
            [
                Client::COMPANY_NAME => $validated[Client::COMPANY_NAME],
            ]
        );
    }
}
