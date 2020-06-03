<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Resources;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Cart $cart
 * @property int $luhn
 * @property Manufacturer $manufacturer
 * @property string $model
 */
class ProductResource extends JsonResource
{

    public const CART_ID = 'cart_id';
    public const CLIENT_COMPANY_NAME = 'company_client_name';
    public const MANUFACTURER_NAME = 'manufacturer_name';
    public const MODEL = 'model';
    public const PRODUCT_ID = 'product_id';

    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            self::CART_ID => $this->cart->luhn ?? null,
            self::CLIENT_COMPANY_NAME => $this->cart->client->company_name ?? null,
            self::MANUFACTURER_NAME => $this->manufacturer->name,
            self::MODEL => $this->model,
            self::PRODUCT_ID => $this->luhn,
        ];
    }
}
