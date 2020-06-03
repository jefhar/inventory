<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Permissions\UserRoles;
use App\User;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public const INDEX_NAME = 'roles.index';
    public const INDEX_PATH = '/dashboard/roles';

    public function index()
    {
        /** @var User $user */
        $user = \Auth::user();
        if ($user->hasRole(UserRoles::SUPER_ADMIN)) {
            $ownerRole = [
                'id' => UserRoles::OWNER,
                'name' => UserRoles::ROLES[UserRoles::OWNER],
            ];
        } else {
            $ownerRole = Role::all()->pluck('name')->except(0)->except(1);
        }

        return $ownerRole;
    }
}
