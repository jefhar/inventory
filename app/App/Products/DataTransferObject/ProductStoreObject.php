<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\DataTransferObject;

use Spatie\DataTransferObject\DataTransferObject;
use Support\Requests\ProductStore;

/**
 * Class ProductStoreObject
 *
 * @package App\Products\DataTransferObject
 * @TODO: Move this namespace to App\Products\DataTransferObjects
 */
class ProductStoreObject extends DataTransferObject
{
    public array $values;
    public int $workorder_id;
    public string $manufacturer_name;
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
                ProductStore::WORK_ORDER_ID => (int)$validated[ProductStore::WORK_ORDER_ID],
                ProductStore::MANUFACTURER_NAME => $validated[ProductStore::MANUFACTURER_NAME],
                ProductStore::MODEL => $validated[ProductStore::MODEL],
                ProductStore::VALUES => $validated[ProductStore::VALUES],
                ProductStore::TYPE => $validated[ProductStore::TYPE],
            ]
        );
    }
}
