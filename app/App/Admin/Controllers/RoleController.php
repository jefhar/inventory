<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\User;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RoleController extends Controller
{
    public const INDEX_NAME = 'roles.index';
    public const INDEX_PATH = '/dashboard/roles';
    public const SHOW_NAME = 'roles.show';
    public const SHOW_PATH = '/dashboard/roles/{role}';

    public function index()
    {
        /** @var User $user */
        $user = \Auth::user();
        $ownerRole = [];
        if ($user->hasRole(UserRoles::SUPER_ADMIN)) {
            $ownerRole[] = [
                'id' => UserRoles::OWNER,
                'name' => Str::title(UserRoles::ROLES[UserRoles::OWNER]),
            ];
        } else {
            foreach ([UserRoles::EMPLOYEE, UserRoles::SALES_REP, UserRoles::TECHNICIAN] as $role) {
                $ownerRole[] = ['id' => $role, 'name' => Str::title(UserRoles::ROLES[$role])];
            }
        }

        return $ownerRole;
    }

    /**
     * @param string $role
     * @return array|null
     */
    public function show($role): ?array
    {
        switch ($role) {
            case UserRoles::EMPLOYEE:
                return UserPermissions::EMPLOYEE_DEFAULT_PERMISSIONS;
            case UserRoles::SALES_REP:
                return UserPermissions::SALES_REP_DEFAULT_PERMISSIONS;
            case UserRoles::TECHNICIAN:
                return UserPermissions::TECHNICIAN_DEFAULT_PERMISSIONS;
            case UserRoles::OWNER:
                return UserPermissions::OWNER_DEFAULT_PERMISSIONS;
            default:
                abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Not strictly needed, because the switch will abort as default.
        return null;
    }
}
