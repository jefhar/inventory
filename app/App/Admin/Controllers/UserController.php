<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Admin\DataTransferObjects\UserStoreObject;
use App\Admin\Permissions\UserRoles;
use App\Admin\Requests\StoreUserRequest;
use App\Admin\Resources\UserResource;
use App\User;
use Domain\Admin\Actions\UserStoreAction;

class UserController extends Controller
{
    public const INDEX_NAME = 'user.index';
    public const INDEX_PATH = '/dashboard/user';
    public const STORE_NAME = 'user.store';
    public const STORE_PATH = '/dashboard/user';

    public function index()
    {
        $users = User::with('roles', 'permissions')->get();
        $nonSuperAdmins = $users->filter(
            function ($value) {
                return $value->roles[0]->name !== UserRoles::SUPER_ADMIN;
            }
        );

        return UserResource::collection($nonSuperAdmins);
    }

    public function store(StoreUserRequest $request)
    {
        $userStoreObject = UserStoreObject::fromRequest($request->validated());

        return new UserResource(UserStoreAction::execute($userStoreObject));
    }
}
