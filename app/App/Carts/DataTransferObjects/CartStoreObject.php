<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\DataTransferObjects;

use Domain\WorkOrders\Models\Client;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class CartStoreObject
 *
 * @package App\Carts\DataTransferObjects
 */
class CartStoreObject extends DataTransferObject
{
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
                'product_id' => (int)$validated['product_id'],
                Client::COMPANY_NAME => $validated[Client::COMPANY_NAME],
            ]
        );
    }
}
