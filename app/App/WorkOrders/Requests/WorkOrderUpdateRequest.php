<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\WorkOrders\Requests;

use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use App\WorkOrders\DataTransferObjects\WorkOrderObject;
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
        self::EMAIL => ['string', 'nullable'],
        self::FIRST_NAME => ['string', 'nullable'],
        self::INTAKE => ['string', 'nullable'],
        self::IS_LOCKED => ['boolean', 'nullable'],
        self::LAST_NAME => ['string', 'nullable'],
        self::PHONE_NUMBER => ['string', 'nullable'],
    ];
    public const CLIENT_COMPANY_NAME = ClientObject::CLIENT_COMPANY_NAME;
    public const EMAIL = PersonObject::EMAIL;
    public const FIRST_NAME = PersonObject::FIRST_NAME;
    public const INTAKE = WorkOrderObject::INTAKE;
    public const IS_LOCKED = WorkOrderObject::IS_LOCKED;
    public const LAST_NAME = PersonObject::LAST_NAME;
    public const PHONE_NUMBER = PersonObject::PHONE_NUMBER;

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
