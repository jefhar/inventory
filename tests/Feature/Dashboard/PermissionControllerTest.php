<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Admin\Controllers\PermissionController;
use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    /**
     * @test
     */
    public function permissionsIndexOnlyVisibleToOwnerAndSuperAdmin(): void
    {
        $this
            ->get(route(PermissionController::INDEX_NAME))
            ->assertRedirect();
        /** @var User $employee */
        $employee = factory(User::class)->create();
        $employee->assignRole(UserRoles::EMPLOYEE);
        $this
            ->actingAs($employee)
            ->get(route(PermissionController::INDEX_NAME))
            ->assertForbidden();

        /** @var User $salesRep */
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $this
            ->actingAs($salesRep)
            ->get(route(PermissionController::INDEX_NAME))
            ->assertForbidden();

        /**  @var User $technician */
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $this
            ->actingAs($technician)
            ->get(route(PermissionController::INDEX_NAME))
            ->assertForbidden();

        /** @var User $owner */
        $owner = factory(User::class)->create();
        $owner->assignRole(UserRoles::OWNER);
        $this
            ->actingAs($owner)
            ->get(route(PermissionController::INDEX_NAME))
            ->assertOk();

        /** @var User $superAdmin */
        $superAdmin = factory(User::class)->create();
        $superAdmin->assignRole(UserRoles::SUPER_ADMIN);
        $this
            ->actingAs($superAdmin)
            ->get(route(PermissionController::INDEX_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function authorizedUserGetsAllPermissions(): void
    {
        /** @var User $owner */
        $owner = factory(User::class)->create();
        $owner->assignRole(UserRoles::OWNER);
        $this
            ->actingAs($owner)
            ->get(route(PermissionController::INDEX_NAME))
            ->assertJsonFragment(
                [
                    [
                        'id' => UserPermissions::IS_EMPLOYEE,
                        'name' => Str::title(UserPermissions::PERMISSIONS[UserPermissions::IS_EMPLOYEE]),
                    ],
                ]
            )
            ->assertJsonMissing(
                [
                    [
                        'id' => UserPermissions::CREATE_OR_EDIT_USERS,
                        'name' => Str::title(UserPermissions::PERMISSIONS[UserPermissions::CREATE_OR_EDIT_USERS]),
                    ],
                ]
            );
    }
}
