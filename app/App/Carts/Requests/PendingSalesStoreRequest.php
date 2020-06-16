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
        self::CART_ID => ['required', 'exists:' . Cart::TABLE . ',' . Cart::LUHN],
        self::PRODUCT_ID => ['required', 'exists:' . Product::TABLE . ',' . Product::LUHN],
    ];
    public const CART_ID = 'cart_id';
    public const PRODUCT_ID = 'product_id';

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
