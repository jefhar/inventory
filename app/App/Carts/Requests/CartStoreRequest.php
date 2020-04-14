<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CartStoreRequest
 *
 * @package App\Carts\Requests
 */
class CartStoreRequest extends FormRequest
{
    private const RULES = [
        'product_id' => ['required', 'exists:' . Product::TABLE . ',' . Product::ID],
        Client::COMPANY_NAME => ['required'],
    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
