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
    public const INDEX_PATH = '/dashboard/users';
    public const STORE_NAME = 'user.store';
    public const STORE_PATH = '/dashboard/users';

    public function index()
    {
        /** @var User $dashboardUser */
        $dashboardUser = \Auth::user();
        $users = User::with('roles', 'permissions')
            ->whereNotIn(User::ID, [$dashboardUser->id])
            ->orderBy(User::NAME)
            ->get();
        $filteredUsers = $users->filter(
            function ($value) {
                return $value->roles[0]->name !== UserRoles::SUPER_ADMIN;
            }
        );
        if ($dashboardUser->hasRole(UserRoles::SUPER_ADMIN)) {
            $filteredUsers = $users->filter(
                function ($value) {
                    return $value->roles[0]->name === UserRoles::OWNER;
                }
            );
        }

        return UserResource::collection($filteredUsers);
    }

    public function store(StoreUserRequest $request)
    {
        $userStoreObject = UserStoreObject::fromRequest($request->validated());

        return new UserResource(UserStoreAction::execute($userStoreObject));
    }
}
