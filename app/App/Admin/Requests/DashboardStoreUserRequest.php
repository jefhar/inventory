<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardStoreUserRequest extends FormRequest
{
    public const EMAIL = 'user.email';
    public const NAME = 'user.name';
    public const PERMISSIONS = 'permissions';
    public const ROLE = 'role';
    public const USER = 'user';
    public const RULES = [
        self::EMAIL => ['email:rfc'],
        self::NAME => ['alpha'],
        self::PERMISSIONS => ['array'],
        self::ROLE => ['exists:roles,name'],
        self::USER => ['array'],
    ];

    public function rules(): array
    {
        return self::RULES;
    }

}
