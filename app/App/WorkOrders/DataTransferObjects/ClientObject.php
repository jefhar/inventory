<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\WorkOrders\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class ClientObject
 *
 * @package App\WorkOrders\DataTransferObjects
 */
class ClientObject extends DataTransferObject
{
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    private const COMPANY_NAME = 'company_name';
    public string $company_name;

    /**
     * @param array $validated
     * @return ClientObject
     */
    public static function fromRequest(array $validated): ClientObject
    {
        return new self(
            [
                self::COMPANY_NAME => $validated[self::CLIENT_COMPANY_NAME] ?? '',
            ]
        );
    }
}
