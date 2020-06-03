<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

class PermissionController extends Controller
{
    public const INDEX_PATH = '/dashboard/permissions';
    public const INDEX_NAME = 'permissions.index';

    public function index()
    {
        return '["Permissions: {}"]';
    }
}
