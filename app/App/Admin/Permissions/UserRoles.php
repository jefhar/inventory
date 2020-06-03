<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace App\Admin\Permissions;

/**
 * Class UserRoles
 *
 * @package App\Admin\Permissions
 */
final class UserRoles
{
    public const EMPLOYEE = 'employee';
    public const OWNER = 'owner';
    public const SALES_REP = 'sales representative';
    public const SUPER_ADMIN = 'super admin';
    public const TECHNICIAN = 'technician';

    public const RULES = [
        self::EMPLOYEE => self::EMPLOYEE,
        self::OWNER => self::OWNER,
        self::SALES_REP => self::SALES_REP,
        self::SUPER_ADMIN => self::SUPER_ADMIN,
        self::TECHNICIAN => self::TECHNICIAN,
    ];
}
