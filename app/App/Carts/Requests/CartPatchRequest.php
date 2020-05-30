<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Requests;

use App\Carts\DataTransferObjects\CartPatchObject;
use Domain\Carts\Models\Cart;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CartPatchRequest extends FormRequest
{
    public const STATUS = CartPatchObject::STATUS;

    public function rules(): array
    {
        return
            [
                self::STATUS => [
                    'required',
                    Rule::in(Cart::STATUSES),
                ],
            ];
    }
}
