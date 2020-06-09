<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Admin\Controllers\DashboardController;
use App\Admin\Permissions\UserRoles;
use App\User;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    /**
     * @test
     */
    public function dashboardIndexOnlyVisibleToOwnerAndSuperAdmin(): void
    {
        $this
            ->get(route(DashboardController::INDEX_NAME))
            ->assertRedirect();
        /** @var User $employee */
        $employee = factory(User::class)->create();
        $employee->assignRole(UserRoles::EMPLOYEE);
        $this
            ->actingAs($employee)
            ->get(route(DashboardController::INDEX_NAME))
            ->assertForbidden();

        /** @var User $salesRep */
        $salesRep = factory(User::class)->create();
        $salesRep->assignRole(UserRoles::SALES_REP);
        $this
            ->actingAs($salesRep)
            ->get(route(DashboardController::INDEX_NAME))
            ->assertForbidden();

        /**  @var User $technician */
        $technician = factory(User::class)->create();
        $technician->assignRole(UserRoles::TECHNICIAN);
        $this
            ->actingAs($technician)
            ->get(route(DashboardController::INDEX_NAME))
            ->assertForbidden();

        /** @var User $owner */
        $owner = factory(User::class)->create();
        $owner->assignRole(UserRoles::OWNER);
        $this
            ->withoutExceptionHandling()
            ->actingAs($owner)
            ->withoutExceptionHandling()
            ->get(route(DashboardController::INDEX_NAME))
            ->assertOk();

        /** @var User $superAdmin */
        $superAdmin = factory(User::class)->create();
        $superAdmin->assignRole(UserRoles::SUPER_ADMIN);
        $this
            ->actingAs($superAdmin)
            ->get(route(DashboardController::INDEX_NAME))
            ->assertOk();
    }
}
