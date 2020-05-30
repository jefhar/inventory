<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\DataTransferObject;

use App\Support\Luhn;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * Class ProductStoreObject
 *
 * @package App\Products\DataTransferObject
 * @TODO: Move this namespace to App\Products\DataTransferObjects
 */
class ProductStoreObject extends DataTransferObject
{
    public const MANUFACTURER_NAME = 'manufacturer_name';
    public const MODEL = 'model';
    public const TYPE = 'type';
    public const WORK_ORDER_ID = 'workorder_id';
    public const VALUES = 'values';
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
                self::WORK_ORDER_ID => Luhn::unLuhn((int)$validated[self::WORK_ORDER_ID]),
                self::MANUFACTURER_NAME => $validated[self::MANUFACTURER_NAME],
                self::MODEL => $validated[self::MODEL],
                self::VALUES => $validated[self::VALUES],
                self::TYPE => $validated[self::TYPE],
            ]
        );
    }
}
