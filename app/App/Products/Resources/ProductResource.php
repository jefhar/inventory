<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Resources;

use Carbon\Carbon;
use Domain\Products\Models\Manufacturer;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Carbon $created_at
 * @property int $luhn
 * @property Manufacturer $manufacturer
 * @property string $model
 * @property string $serial
 * @property string $type
 * @property mixed $workorder
 */
class ProductResource extends JsonResource
{

    public const CREATED_AT = 'created_at';
    public const ID = 'product_id';
    public const MANUFACTURER_NAME = 'manufacturer_name';
    public const MODEL = 'model';
    public const SERIAL = 'serial';
    public const TYPE = 'type';
    public const WORK_ORDER_ID = 'work_order_id';

    public function toArray($request): array
    {
        return [
            self::CREATED_AT => $this->created_at->format('Y-m-d H:i:s'),
            self::ID => $this->luhn,
            self::MANUFACTURER_NAME => $this->manufacturer->name,
            self::MODEL => $this->model,
            self::SERIAL => $this->serial,
            self::TYPE => $this->type,
            self::WORK_ORDER_ID => $this->workorder->luhn,

            // self::PRICE => $this->price,
            // self::STATUS => $this->status,
            // self::TYPE_ID => $this->type_id,
            // self::UPDATED_AT => $this->updated_at->format('Y-m-d H:i:s'),
            // self::VALUES => $this->values,
        ];
    }
}
