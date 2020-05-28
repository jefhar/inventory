<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use Domain\Products\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Support\Requests\CartStore;

/**
 * Class CartStoreRequest
 *
 * @package App\Carts\Requests
 */
class CartStoreRequest extends FormRequest
{
    private const RULES = [
        CartStore::PRODUCT_ID => ['required', 'exists:' . Product::TABLE . ',' . Product::ID],
        CartStore::COMPANY_NAME => ['required'],
    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
