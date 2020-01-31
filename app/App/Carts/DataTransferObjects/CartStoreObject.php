<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class CartStoreObject
 *
 * @package App\Carts\DataTransferObjects
 */
class CartStoreObject extends DataTransferObject
{

    /**
     * @param array $validated
     * @return CartStoreObject
     */
    public static function fromRequest(array $validated): self
    {
        return new self(
            [

            ]
        );
    }
}
