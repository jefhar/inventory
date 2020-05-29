<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\DataTransferObjects;

use App\Carts\Requests\PendingSalesStoreRequest;
use App\Support\Luhn;
use Spatie\DataTransferObject\DataTransferObject;

class PendingSalesStoreObject extends DataTransferObject
{

    public int $cart_id;
    public int $product_id;

    public static function fromRequest(array $validated)
    {
        return new self(
            [
                PendingSalesStoreRequest::CART_ID => Luhn::unLuhn($validated[PendingSalesStoreRequest::CART_ID]),
                PendingSalesStoreRequest::PRODUCT_ID => Luhn::unLuhn($validated[PendingSalesStoreRequest::PRODUCT_ID]),
            ]
        );
    }
}
