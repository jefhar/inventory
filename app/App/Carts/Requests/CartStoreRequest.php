<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CartStoreRequest
 *
 * @package App\Carts\Requests
 */
class CartStoreRequest extends FormRequest
{
    private const RULES = [

    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
