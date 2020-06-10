<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\DataTransferObjects\DashboardUserObject;
use App\Admin\Requests\DashboardStoreUserRequest;
use App\User;
use Domain\Admin\Actions\DashboardCreateUserAction;

class DashboardController extends Controller
{
    public const INDEX_NAME = 'dashboard.index';
    public const INDEX_PATH = '/dashboard';
    public const STORE_NAME = 'dashboard.store';
    public const STORE_PATH = '/dashboard';

    public function index()
    {
        return view('dashboard.index');
    }

    public function store(DashboardStoreUserRequest $request): User
    {
        $dashboardUserObject = DashboardUserObject::fromRequest($request->validated());

        // @todo: Buid a UserJsonResource
        return DashboardCreateUserAction::execute($dashboardUserObject);
    }
}
