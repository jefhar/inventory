<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Requests;

use Domain\Products\Models\Type;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ProductStoreRequest
 *
 * @package App\Products\Requests
 */
class ProductStoreRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'manufacturer' => ['required'],
            'model' => ['required'],
            'type' => ['required', 'exists:' . Type::TABLE . ',' . Type::SLUG],
            'workOrderId' => ['required', 'exists:' . WorkOrder::TABLE . ',' . WorkOrder::LUHN],
            'values' => ['array'],
        ];
    }
}
