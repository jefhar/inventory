<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Admin\Actions;

use App\Admin\DataTransferObjects\DashboardUserObject;
use App\User;
use Illuminate\Support\Facades\Hash;

class DashboardCreateUserAction
{

    public static function execute(DashboardUserObject $dashboardUserObject): User
    {
        $user = User::firstOrCreate(
            [
                User::EMAIL => $dashboardUserObject->user['email'],
            ],
            [
                User::NAME => $dashboardUserObject->user['name'],
                User::PASSWORD => Hash::make(bin2hex(random_bytes(48))),
            ]
        );
        $user->assignRole($dashboardUserObject->role);
        $user->givePermissionTo($dashboardUserObject->permissions);
        $user->save();

        return $user;
    }
}
