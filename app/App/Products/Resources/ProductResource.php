<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Resources;

use Domain\Products\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request): array
    {
        return [
            Product::CREATED_AT => $this->created_at->format('Y-m-d H:i:s'),
            Product::ID => $this->luhn,
            Product::MANUFACTURER_NAME => $this->manufacturer->name,
            Product::MODEL => $this->model,
            Product::SERIAL => $this->serial,
            Product::TYPE => $this->type,

            // Product::PRICE => $this->price,
            // Product::STATUS => $this->status,
            // Product::TYPE_ID => $this->type_id,
            // Product::UPDATED_AT => $this->updated_at->format('Y-m-d H:i:s'),
            // Product::VALUES => $this->values,
            // Product::WORK_ORDER_ID => $this->workorder->luhn,
        ];
    }
}
