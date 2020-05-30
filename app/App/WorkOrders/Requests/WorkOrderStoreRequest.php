<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Requests;

use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
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
        self::FIRST_NAME => ['string', 'nullable'],
        self::LAST_NAME => ['string', 'nullable'],
        self::PHONE_NUMBER => ['string', 'nullable', 'min:10', 'max:16'],
        self::EMAIL => ['string', 'nullable'],
    ];
    public const CLIENT_COMPANY_NAME = ClientObject::CLIENT_COMPANY_NAME;
    public const EMAIL = PersonObject::EMAIL;
    public const FIRST_NAME = PersonObject::FIRST_NAME;
    public const LAST_NAME = PersonObject::LAST_NAME;
    public const PHONE_NUMBER = PersonObject::PHONE_NUMBER;

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
            self::CLIENT_COMPANY_NAME => 'Company Name',
        ];
    }
}
