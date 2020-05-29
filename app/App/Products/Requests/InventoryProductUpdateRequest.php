<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Requests;

use App\Admin\Permissions\UserPermissions;
use App\User;
use Domain\Products\Models\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class InventoryProductUpdateRequest
 *
 * @package App\Products\Requests
 */
class InventoryProductUpdateRequest extends FormRequest
{
    public const MANUFACTURER_NAME = 'manufacturer_name';
    public const TYPE = 'type';
    public const VALUES = 'values';
    public const MODEL = 'model';

    public const RULES = [
        self::MANUFACTURER_NAME => ['required'],
        self::MODEL => ['required'],
        self::TYPE => ['required', 'exists:' . Type::TABLE . ',' . Type::SLUG],
        self::VALUES => ['array'],
    ];

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->hasPermissionTo(UserPermissions::UPDATE_RAW_PRODUCTS);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
