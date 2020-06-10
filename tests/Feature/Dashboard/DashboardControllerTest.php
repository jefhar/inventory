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
use Tests\Traits\FullObjects;

class DashboardControllerTest extends TestCase
{
    use FullObjects;

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

    /**
     * @test
     * @throws \JsonException
     */
    public function dashboardCanSaveNewUser(): void
    {
        $postData = '{"permissions":["user.is.employee","product.price.update","cart.mutate","inventoryItem.view.edit","carts.view.all_open","product.raw.update"],"role":"sales representative","user":{"email":"bob@svbamm.com","name":"bob"}}';
        $postDataArray = json_decode($postData, true, 512, JSON_THROW_ON_ERROR);
        $this
            ->withoutExceptionHandling()
            ->actingAs($this->createEmployee(UserRoles::OWNER))
            ->post(route(DashboardController::STORE_NAME), $postDataArray);
        $this->assertDatabaseHas(
            User::TABLE,
            [
                User::NAME => $postDataArray['user']['name'],
                User::EMAIL => $postDataArray['user']['email'],
            ]
        );

        $user = User::where(User::EMAIL, $postDataArray['user']['email'])->first();
        $this->assertTrue($user->hasAllPermissions($postDataArray['permissions']));
    }


}
