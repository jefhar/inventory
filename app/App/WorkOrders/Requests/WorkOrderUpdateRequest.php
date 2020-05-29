<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class WorkOrderUpdateRequest
 *
 * @package App\WorkOrders\Requests
 */
class WorkOrderUpdateRequest extends FormRequest
{
    private const RULES = [
        self::CLIENT_COMPANY_NAME => ['string', 'nullable'],
        self::FIRST_NAME => ['string', 'nullable'],
        self::LAST_NAME => ['string', 'nullable'],
        self::PHONE_NUMBER => ['string', 'nullable'],
        self::EMAIL => ['string', 'nullable'],
        self::INTAKE => ['string', 'nullable'],
        self::IS_LOCKED => ['boolean', 'nullable'],
    ];
    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const EMAIL = 'email';
    public const FIRST_NAME = 'first_name';
    public const INTAKE = 'intake';
    public const IS_LOCKED = 'is_locked';
    public const LAST_NAME = 'last_name';
    public const PHONE_NUMBER = 'phone_number';

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
