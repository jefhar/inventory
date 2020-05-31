<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Traits;

use App\Admin\Permissions\UserRoles;
use App\User;

/**
 * Trait FullUsers
 *
 * @package Tests\Traits
 */
trait FullUsers
{

    /**
     * @param string $userRole
     * @return User
     */
    private function createEmployee(string $userRole = UserRoles::EMPLOYEE): User
    {
        $user = $this->makeUser();
        $user->assignRole($userRole);
        $user->save();

        return $user;
    }

    /**
     * @return User
     */
    private function makeUser(): User
    {
        return factory(User::class)->make();
    }
}
