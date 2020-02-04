<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\DataTransferObject;

use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class ProductStoreObject
 *
 * @package App\Products\DataTransferObject
 * @TODO: Move this namespace to App\Products\DataTransferObjects
 */
class ProductStoreObject extends DataTransferObject
{
    public array $values;
    public int $workOrderId;
    public string $manufacturer;
    public string $model;
    public string $type;

    /**
     * @param array $validated
     * @return ProductStoreObject
     */
    public static function fromRequest(array $validated): ProductStoreObject
    {
        return new self(
            [
                'workOrderId' => (int)$validated['workOrderId'],
                Manufacturer::MANUFACTURER => $validated[Manufacturer::MANUFACTURER],
                Product::MODEL => $validated[Product::MODEL],
                Product::VALUES => $validated[Product::VALUES],
                Type::TYPE => $validated[Type::TYPE],
            ]
        );
    }
}
