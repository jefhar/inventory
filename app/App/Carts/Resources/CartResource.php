<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Resources;

use Domain\WorkOrders\Models\Client;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Client $client
 * @property int $luhn
 * @property string $status
 */
class CartResource extends JsonResource
{

    public const CART_ID = 'cart_id';
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const STATUS = 'status';

    public function toArray($request): array
    {
        return
            [
                self::CART_ID => $this->luhn,
                self::CLIENT_COMPANY_NAME => $this->client->company_name,
                self::STATUS => $this->status,
            ];
    }
}
