<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature\Dashboard;

use App\Admin\Controllers\UserController;
use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\Admin\Resources\UserResource;
use App\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Tests\Traits\FullObjects;

class UserControllerTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     * @throws \JsonException
     * @throws \Exception
     */
    public function dashboardCanSaveNewUser(): void
    {
        $postData = '{"permissions":["user.is.employee","product.price.update","cart.mutate","inventoryItem.view.edit","carts.view.all_open","product.raw.update"],"role":"sales representative","user":{"email":"bob@example.com","name":"bob"}}';
        $postDataArray = json_decode($postData, true, 512, JSON_THROW_ON_ERROR);
        $this
            ->withoutExceptionHandling()
            ->actingAs($this->createEmployee(UserRoles::OWNER))
            ->post(route(UserController::STORE_NAME), $postDataArray)
            ->assertJsonMissing([User::PASSWORD])
            ->assertJson(
                [
                    User::NAME => $postDataArray['user']['name'],
                    User::EMAIL => $postDataArray['user']['email'],
                ]
            );
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

    /**
     * @test
     */
    public function ownerUserCanFetchAllUsers(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::OWNER));
        for ($i = 0; $i < 5; ++$i) {
            $this->createEmployee(UserRoles::EMPLOYEE)
                ->givePermissionTo(UserPermissions::EMPLOYEE_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::SALES_REP)
                ->givePermissionTo(UserPermissions::SALES_REP_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::TECHNICIAN)
                ->givePermissionTo(UserPermissions::TECHNICIAN_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::OWNER)
                ->givePermissionTo(UserPermissions::OWNER_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::SUPER_ADMIN)
                ->givePermissionTo(Permission::all());
        }

        $this->get(
            route(UserController::INDEX_NAME)
        )->assertJsonStructure(
            [
                [
                    UserResource::EMAIL,
                    UserResource::NAME,
                    UserResource::PERMISSIONS,
                    UserResource::ROLE,
                ],
            ]
        )
            ->assertDontSee(User::PASSWORD)
            ->assertDontSee(UserRoles::SUPER_ADMIN)
            ->assertSee(userRoles::EMPLOYEE)
            ->assertSee(UserRoles::OWNER)
            ->assertSee(UserRoles::SALES_REP)
            ->assertSee(UserRoles::TECHNICIAN)
        ;
    }

    /**
     * @test
     */
    public function superAdminUserCanOnlyFetchOwnerUsers(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::SUPER_ADMIN));
        for ($i = 0; $i < 5; ++$i) {
            $this->createEmployee(UserRoles::EMPLOYEE)
                ->givePermissionTo(UserPermissions::EMPLOYEE_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::SALES_REP)
                ->givePermissionTo(UserPermissions::SALES_REP_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::TECHNICIAN)
                ->givePermissionTo(UserPermissions::TECHNICIAN_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::OWNER)
                ->givePermissionTo(UserPermissions::OWNER_DEFAULT_PERMISSIONS);
            $this->createEmployee(UserRoles::SUPER_ADMIN)
                ->givePermissionTo(Permission::all());
        }

        $this->get(
            route(UserController::INDEX_NAME)
        )->assertJsonStructure(
            [
                [
                    UserResource::EMAIL,
                    UserResource::NAME,
                    UserResource::PERMISSIONS,
                    UserResource::ROLE,
                ],
            ]
        )
            ->assertDontSee(User::PASSWORD)
            ->assertDontSee('"name":"' . UserRoles::EMPLOYEE)
            ->assertDontSee(UserRoles::SALES_REP)
            ->assertDontSee(UserRoles::SUPER_ADMIN)
            ->assertDontSee(UserRoles::TECHNICIAN)
            ->assertSee(UserRoles::OWNER)
        ;
    }
}
