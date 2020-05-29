<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Requests;

use Domain\WorkOrders\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WorkOrderStoreRequest
 *
 * @package App\Admin\Requests
 */
class WorkOrderStoreRequest extends FormRequest
{
    private const RULES = [
        self::CLIENT_COMPANY_NAME => ['required', 'string'],
        self::CLIENT_FIRST_NAME => ['string', 'nullable'],
        self::CLIENT_LAST_NAME => ['string', 'nullable'],
        self::PHONE_NUMBER => ['string', 'nullable', 'min:10', 'max:16'],
        self::EMAIL => ['string', 'nullable'],
    ];
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const CLIENT_FIRST_NAME = 'first_name';
    public const CLIENT_LAST_NAME = 'last_name';
    public const EMAIL = 'email';
    public const PHONE_NUMBER = 'phone_number';

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
