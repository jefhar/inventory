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
class ProductUpdateObject extends DataTransferObject
{
    public array $values;
    public string $manufacturer;
    public string $model;
    public string $type;

    /**
     * @param array $validated
     * @return ProductUpdateObject
     */
    public static function fromRequest(array $validated): ProductUpdateObject
    {
        return new self(
            [
                'manufacturer' => $validated['manufacturer'],
                'type' => $validated['type'],
                Product::MODEL => $validated[Product::MODEL],
                Product::VALUES => $validated[Product::VALUES],
            ]
        );
    }
}
