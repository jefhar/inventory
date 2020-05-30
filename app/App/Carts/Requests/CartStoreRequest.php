<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use App\Carts\DataTransferObjects\CartStoreObject;
use Domain\Products\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CartStoreRequest
 *
 * @package App\Carts\Requests
 */
class CartStoreRequest extends FormRequest
{
    public const CLIENT_COMPANY_NAME = CartStoreObject::CLIENT_COMPANY_NAME;
    public const PRODUCT_ID = CartStoreObject::PRODUCT_ID;

    private const RULES = [
        self::PRODUCT_ID => ['required', 'exists:' . Product::TABLE . ',' . Product::LUHN],
        self::CLIENT_COMPANY_NAME => ['required'],
    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
