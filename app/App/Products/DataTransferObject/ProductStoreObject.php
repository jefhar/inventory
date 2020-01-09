<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class ProductStoreObject
 *
 * @package App\Products\DataTransferObject
 * @TODO: Move this namespace to App\Products\DataTransferObjects
 */
class ProductStoreObject extends DataTransferObject
{
    public int $workOrderId;
    public string $type;
    public string $manufacturer;
    public string $model;
    public array $values;

    /**
     * @param array $validated
     * @return ProductStoreObject
     */
    public static function fromRequest(array $validated): ProductStoreObject
    {
        return new self(
            [
                'workOrderId' => (int)$validated['workOrderId'],
                'type' => $validated['type'],
                'manufacturer' => $validated['manufacturer'],
                'model' => $validated['model'],
                'values' => $validated['values'],
            ]
        );
    }
}
