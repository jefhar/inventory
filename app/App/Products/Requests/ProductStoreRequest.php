<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Requests;

use Domain\Products\Models\Type;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;
use Support\Requests\ProductStore;

/**
 * Class ProductStoreRequest
 *
 * @package App\Products\Requests
 */
class ProductStoreRequest extends FormRequest
{
    public const RULES = [
        ProductStore::WORK_ORDER_ID => ['required', 'exists:' . WorkOrder::TABLE . ',' . WorkOrder::LUHN],
        ProductStore::MANUFACTURER_NAME => ['required'],
        ProductStore::MODEL => ['required'],
        ProductStore::VALUES => ['array'],
        ProductStore::TYPE => ['required', 'exists:' . Type::TABLE . ',' . Type::SLUG],
    ];

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
        return self::RULES;
    }
}
