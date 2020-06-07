<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

class DashboardController extends Controller
{
    public const INDEX_NAME = 'dashboard.index';
    public const INDEX_PATH = '/dashboard';

    public function index()
    {
        return view('dashboard.index');
    }
}
