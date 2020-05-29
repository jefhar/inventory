<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use Domain\Products\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CartStoreRequest
 *
 * @package App\Carts\Requests
 */
class CartStoreRequest extends FormRequest
{
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const FIRST_NAME = 'first_name';
    public const PRODUCT_ID = 'product_id';

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
