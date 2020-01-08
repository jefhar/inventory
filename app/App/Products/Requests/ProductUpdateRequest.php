<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Requests;

use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\Products\Models\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class ProductUpdateRequest
 *
 * @package App\Products\Requests
 */
class ProductUpdateRequest extends FormRequest
{
    public const RULES = [
        'manufacturer' => ['required'],
        'model' => ['required'],
        'type' => ['required', 'exists:' . Type::TABLE . ',' . Type::SLUG],
        'values' => ['array'],
    ];

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        if (
            $user->hasAnyRole(
                [
                UserRoles::TECHNICIAN,
                UserRoles::OWNER,
                UserRoles::SUPER_ADMIN,
                ]
            )
        ) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return self::RULES;
    }
}
