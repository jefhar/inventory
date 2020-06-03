<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

class RoleController extends Controller
{
    public const INDEX_NAME = 'roles.index';
    public const INDEX_PATH = '/dashboard/roles';

    public function index()
    {
        return '["Roles: {}"]';
    }
}
