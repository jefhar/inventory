<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Requests;

use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WorkOrderUpdateRequest
 *
 * @package App\WorkOrders\Requests
 */
class WorkOrderUpdateRequest extends FormRequest
{
    public const RULES = [
        Client::COMPANY_NAME => 'string|nullable',
        Person::FIRST_NAME => 'string|nullable',
        Person::LAST_NAME => 'string|nullable',
        Person::PHONE_NUMBER => 'string|nullable',
        Person::EMAIL => 'string|nullable',
        WorkOrder::INTAKE => 'string|nullable',
        WorkOrder::IS_LOCKED => 'boolean|nullable',
    ];

    /**
     * Determine if the user is authorized to make this request.
     * Here's a quick tip: If there is no authorize() function, the parent
     * class returns true to authorize();
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
