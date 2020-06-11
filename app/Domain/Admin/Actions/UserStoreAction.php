<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Admin\Actions;

use App\Admin\DataTransferObjects\UserStoreObject;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserStoreAction
{

    public static function execute(UserStoreObject $dashboardUserObject): User
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
        // TODO: send email to new user telling them an account has been created and they need to change the password.

        return $user;
    }
}
