<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Requests;

use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WorkOrderStoreRequest
 *
 * @package App\Admin\Requests
 */
class WorkOrderStoreRequest extends FormRequest
{
    private const RULES = [
        Client::COMPANY_NAME => ['required', 'string'],
        Person::FIRST_NAME => ['string', 'nullable'],
        Person::LAST_NAME => ['string', 'nullable'],
        Person::PHONE_NUMBER => ['string', 'nullable', 'min:10', 'max:16'],
        Person::EMAIL => ['string', 'nullable'],
    ];

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
        return self::RULES;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            Client::COMPANY_NAME => 'Company Name',
        ];
    }
}
