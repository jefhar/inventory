<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Resources;

use App\Admin\Permissions\UserPermissions as Permission;
use App\Admin\Permissions\UserRoles as Role;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property Permission[] $permissions
 * @property string $name
 * @property string $email
 * @property Role[] $roles
 */
class UserResource extends JsonResource
{

    public const NAME = User::NAME;
    public const EMAIL = User::EMAIL;
    public const ROLE = 'role';
    public const PERMISSIONS = 'permissions';

    public function toArray($request)
    {
        $permissions = [];
        foreach ($this->permissions as $permission) {
            $permissions[] = $permission->name;
        }

        return [
            self::EMAIL => $this->email,
            self::NAME => $this->name,
            self::PERMISSIONS => $permissions,
            self::ROLE => $this->roles[0]->name ?? null,
        ];
    }
}
