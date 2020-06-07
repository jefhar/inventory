<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\Permissions\UserPermissions;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public const INDEX_PATH = '/dashboard/permissions';
    public const INDEX_NAME = 'permissions.index';

    public function index()
    {
        $permissions = [];

        foreach (
            [
                UserPermissions::CREATE_OR_EDIT_PRODUCT_TYPE,
                UserPermissions::MUTATE_PRODUCT_VALUES,
                UserPermissions::IS_EMPLOYEE,
                UserPermissions::MUTATE_CART,
                UserPermissions::MUTATE_PRODUCT_VALUES,
                UserPermissions::SEE_ALL_OPEN_CARTS,
                UserPermissions::UPDATE_PRODUCT_PRICE,
                UserPermissions::UPDATE_RAW_PRODUCTS,
                UserPermissions::WORK_ORDER_OPTIONAL_PERSON,
            ] as $permission
        ) {
            $permissions[] = ['id' => $permission, 'name' => Str::title(UserPermissions::PERMISSIONS[$permission])];
        }

        return $permissions;
    }
}
