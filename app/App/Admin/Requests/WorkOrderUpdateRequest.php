<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Requests;

use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;

class WorkOrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
        return [
            Client::COMPANY_NAME => 'string|nullable',
            Person::FIRST_NAME => 'string|nullable',
            Person::LAST_NAME => 'string|nullable',
            Person::PHONE_NUMBER => 'string|nullable',
            Person::EMAIL => 'string|nullable',
            WorkOrder::INTAKE => 'string|nullable',
            WorkOrder::IS_LOCKED => 'boolean|nullable',
        ];
    }
}
