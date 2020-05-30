<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Requests;

use App\Products\DataTransferObject\ProductStoreObject;
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
    public const RULES = [
        self::WORK_ORDER_ID => ['required', 'exists:' . WorkOrder::TABLE . ',' . WorkOrder::LUHN],
        self::MANUFACTURER_NAME => ['required'],
        self::MODEL => ['required'],
        self::VALUES => ['array'],
        self::TYPE => ['required', 'exists:' . Type::TABLE . ',' . Type::SLUG],
    ];
    public const MANUFACTURER_NAME = ProductStoreObject::MANUFACTURER_NAME;
    public const MODEL = ProductStoreObject::MODEL;
    public const TYPE = ProductStoreObject::TYPE;
    public const WORK_ORDER_ID = ProductStoreObject::WORK_ORDER_ID;
    public const VALUES = ProductStoreObject::VALUES;

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
