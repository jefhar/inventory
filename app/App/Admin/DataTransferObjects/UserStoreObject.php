<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\DataTransferObjects;

use App\Admin\Requests\StoreUserRequest;
use Spatie\DataTransferObject\DataTransferObject;

class UserStoreObject extends DataTransferObject
{
    public const USER = 'user';
    public const ROLE = 'role';
    public const PERMISSIONS = 'permissions';

    public array $user;
    public string $role;
    public array $permissions;

    public static function fromRequest(array $validated): UserStoreObject
    {
        return new self(
            [
                self::USER => $validated[StoreUserRequest::USER],
                self::ROLE => $validated[StoreUserRequest::ROLE],
                self::PERMISSIONS => $validated[StoreUserRequest::PERMISSIONS],
            ]
        );
    }
}
