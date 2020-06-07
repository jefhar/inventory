<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\DataTransferObjects;

use App\Support\Luhn;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class CartStoreObject
 *
 * @package App\Carts\DataTransferObjects
 */
class CartStoreObject extends DataTransferObject
{
    private const COMPANY_NAME = 'company_name';
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const PRODUCT_ID = 'product_id';
    public int $product_id;
    public string $company_name;

    /**
     * @param array $validated
     * @return CartStoreObject
     */
    public static function fromRequest(array $validated): CartStoreObject
    {
        return new self(
            [
                self::COMPANY_NAME => $validated[self::CLIENT_COMPANY_NAME],
                self::PRODUCT_ID => Luhn::unLuhn((int)$validated[self::PRODUCT_ID]),
            ]
        );
    }
}
