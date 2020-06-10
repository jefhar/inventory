<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Admin\Controllers\RoleController;
use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\FullObjects;

class RoleControllerTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function guestsCannotSeeDashboard(): void
    {
        $this->expectException(AuthenticationException::class);
        $this
            ->withoutExceptionHandling()
            ->get(route(RoleController::INDEX_NAME));
    }

    /**
     * @test
     */
    public function rolesIndexOnlyVisibleToOwnerAndSuperAdmin(): void
    {
        $employee = $this->createEmployee(UserRoles::EMPLOYEE);
        $this
            ->actingAs($employee)
            ->get(route(RoleController::INDEX_NAME))
            ->assertForbidden();

        $salesRep = $this->createEmployee(UserRoles::SALES_REP);
        $this
            ->actingAs($salesRep)
            ->get(route(RoleController::INDEX_NAME))
            ->assertForbidden();

        $technician = $this->createEmployee(UserRoles::TECHNICIAN);
        $this
            ->actingAs($technician)
            ->get(route(RoleController::INDEX_NAME))
            ->assertForbidden();

        $owner = $this->createEmployee(UserRoles::OWNER);
        $this
            ->withoutExceptionHandling()
            ->actingAs($owner)
            ->get(route(RoleController::INDEX_NAME))
            ->assertOk();

        /** @var User $superAdmin */
        $superAdmin = $this->createEmployee(UserRoles::SUPER_ADMIN);
        $this
            ->actingAs($superAdmin)
            ->get(route(RoleController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function superAdminReceivesOnlyOwnerRole(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::SUPER_ADMIN);
        $this->actingAs($user)
            ->get(route(RoleController::INDEX_NAME))
            ->assertOk()
            ->assertJson(
                [
                    ['id' => UserRoles::OWNER, 'name' => Str::title(UserRoles::ROLES[UserRoles::OWNER]),],
                ],
            )->assertJsonMissing(
                [
                    ['id' => UserRoles::SUPER_ADMIN, 'name' => Str::title(UserRoles::ROLES[UserRoles::SUPER_ADMIN]),],
                ]
            );
    }

    /**
     * @test
     */
    public function ownerReceivesNonAdministrativeRoles(): void
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::OWNER);
        $this->actingAs($user)
            ->get(route(RoleController::INDEX_NAME))
            ->assertOk()
            ->assertJsonMissing(
                [
                    ['id' => UserRoles::OWNER, 'name' => UserRoles::ROLES[UserRoles::OWNER],],
                ]
            )
            ->assertJsonMissing(
                [
                    ['id' => UserRoles::SUPER_ADMIN, 'name' => UserRoles::ROLES[UserRoles::SUPER_ADMIN],],
                ]
            )
            ->assertJson(
                [
                    ['id' => UserRoles::EMPLOYEE, 'name' => Str::title(UserRoles::ROLES[UserRoles::EMPLOYEE]),],
                ]
            );
    }

    /**
     * @test
     */
    public function roleShowEmployeeReturnsBasicPermission(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::OWNER))
            ->get(
                route(RoleController::SHOW_NAME, UserRoles::EMPLOYEE)
            )->assertJson(UserPermissions::EMPLOYEE_DEFAULT_PERMISSIONS);
    }
}
