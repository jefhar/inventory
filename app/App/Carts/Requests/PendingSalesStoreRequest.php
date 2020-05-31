<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PendingSalesStoreRequest
 *
 * @package App\Carts\Controllers\App\Carts\Requests
 */
class PendingSalesStoreRequest extends FormRequest
{
    private const RULES = [
        Product::CART_ID => ['required', 'exists:' . Cart::TABLE . ',' . Cart::ID],
        Product::ID => ['required', 'exists:' . Product::TABLE . ',' . Product::ID],
    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
