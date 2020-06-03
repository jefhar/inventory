<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Permissions\UserRoles;
use App\User;

class RoleController extends Controller
{
    public const INDEX_NAME = 'roles.index';
    public const INDEX_PATH = '/dashboard/roles';

    public function index()
    {
        /** @var User $user */
        $user = \Auth::user();
        $ownerRole = [];
        if ($user->hasRole(UserRoles::SUPER_ADMIN)) {
            $ownerRole[] = [
                'id' => UserRoles::OWNER,
                'name' => UserRoles::ROLES[UserRoles::OWNER],
            ];
        } else {
            foreach ([UserRoles::EMPLOYEE, UserRoles::SALES_REP, UserRoles::TECHNICIAN] as $role) {
                $ownerRole[] = ['id' => $role, 'name' => UserRoles::ROLES[$role]];
            }
        }

        return $ownerRole;
    }
}
