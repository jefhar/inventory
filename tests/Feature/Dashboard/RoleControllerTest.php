<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Admin\Controllers\RoleController;
use App\Admin\Permissions\UserRoles;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
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
        /** @var User $employee */
        $employee = factory(User::class)->create();
        $employee->assignRole(UserRoles::EMPLOYEE);
        $this
            ->actingAs($employee)
            ->get(route(RoleController::INDEX_NAME))
            ->assertForbidden();

        /** @var User $salesRep */
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $this
            ->actingAs($salesRep)
            ->get(route(RoleController::INDEX_NAME))
            ->assertForbidden();

        /**  @var User $technician */
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $this
            ->actingAs($technician)
            ->get(route(RoleController::INDEX_NAME))
            ->assertForbidden();

        /** @var User $owner */
        $owner = factory(User::class)->create();
        $owner->assignRole(UserRoles::OWNER);
        $this
            ->withoutExceptionHandling()
            ->actingAs($owner)
            ->get(route(RoleController::INDEX_NAME))
            ->assertOk();

        /** @var User $superAdmin */
        $superAdmin = factory(User::class)->create();
        $superAdmin->assignRole(UserRoles::SUPER_ADMIN);
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
                    ['id' => UserRoles::OWNER, 'name' => UserRoles::ROLES[UserRoles::OWNER],],
                ],
            )->assertJsonMissing(
                [
                    ['id' => UserRoles::SUPER_ADMIN, 'name' => UserRoles::ROLES[UserRoles::SUPER_ADMIN],],
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
                    ['id' => UserRoles::EMPLOYEE, 'name' => UserRoles::ROLES[UserRoles::EMPLOYEE],],
                ]
            );
    }
}
