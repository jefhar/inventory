<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Resources;

use Carbon\Carbon;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Cart $cart
 * @property float $price
 * @property int $luhn
 * @property int $work_order_id
 * @property Manufacturer $manufacturer
 * @property string $model
 * @property string $serial
 * @property string $status
 * @property Type $type
 * @property Carbon created_at
 */
class ProductResource extends JsonResource
{
    public const CART_ID = 'cart_id';
    public const CLIENT_COMPANY_NAME = 'company_client_name';
    public const CREATED_AT = 'created_at';
    public const MANUFACTURER_NAME = 'manufacturer_name';
    public const MODEL = 'model';
    public const PRICE = 'price';
    public const PRODUCT_ID = 'product_id';
    public const SERIAL = 'serial';
    public const STATUS = 'status';
    public const TYPE_NAME = 'type_name';
    public const WORK_ORDER_ID = 'work_order_id';

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $translation = [
            self::CART_ID => $this->cart->luhn ?? null,
            self::CLIENT_COMPANY_NAME => $this->cart->client->company_name ?? null,
            self::CREATED_AT => $this->created_at->format('j M Y H:i'),
            self::MANUFACTURER_NAME => $this->manufacturer->name,
            self::MODEL => $this->model,
            self::PRICE => $this->price,
            self::SERIAL => $this->serial,
            self::STATUS => $this->status,
            self::TYPE_NAME => $this->type->name,
            self::WORK_ORDER_ID => $this->work_order_id,
        ];
        // Place model_id first
        array_unshift($translation, [self::PRODUCT_ID => $this->luhn]);

        return $translation;
    }
}
