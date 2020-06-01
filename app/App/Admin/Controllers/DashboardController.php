<?php

namespace App\Admin\Controllers;

class DashboardController extends Controller
{
    public const CREATE_NAME = 'dashboard.create';

    public function create()
    {
        return view('dashboard.create');
    }
}
