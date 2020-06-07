<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\DataTransferObject;

use Domain\Products\Models\Product;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class ProductUpdateObject
 *
 * @package App\Products\DataTransferObject
 */
class InventoryProductUpdateObject extends DataTransferObject
{
    public array $values;
    public string $manufacturer_name;
    public string $model;
    public string $type;

    /**
     * @param array $validated
     * @return InventoryProductUpdateObject
     */
    public static function fromRequest(array $validated): InventoryProductUpdateObject
    {
        return new self(
            [
                Product::MANUFACTURER_NAME => $validated[Product::MANUFACTURER_NAME],
                Product::TYPE => $validated[Product::TYPE],
                Product::MODEL => $validated[Product::MODEL],
                Product::VALUES => $validated[Product::VALUES],
            ]
        );
    }
}
