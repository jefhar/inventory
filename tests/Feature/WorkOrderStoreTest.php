<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Controllers\WorkOrdersController;
use App\Admin\Permissions\UserPermissions;
use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class WorkOrderStoreTest extends TestCase
{
    use RefreshDatabase;

    private const COMPANY_NAME = 'George Q. Client';
    private User $anonymousUser;
    private User $authorizedUser;
    private User $unauthorizedUser;
    private User $technicianUser;

    /**
     * @test
     */
    public function unauthorizedCreateIsUnauthorized(): void
    {
        $this->actingAs($this->anonymousUser)
            ->get(
                route(WorkOrdersController::CREATE_NAME)
            )->assertUnauthorized()
            ->assertCookieMissing('techUser');

        $this->actingAs($this->unauthorizedUser)
            ->get(
                route(WorkOrdersController::CREATE_NAME)
            )->assertUnauthorized()
            ->assertCookieMissing('techUser');
    }

    /**
     * @test
     */
    public function authorizedCreateIsOk(): void
    {
        $this->actingAs($this->authorizedUser)
            ->get(
                route(WorkOrdersController::CREATE_NAME)
            )->assertOk()
            ->assertCookieMissing('techUser');

        $this->actingAs($this->technicianUser)
            ->get(
                route(WorkOrdersController::CREATE_NAME)
            )->assertOK()
            ->assertCookie('techUser', true);
    }

    /**
     * @test
     */
    public function unauthorizedStoreIsUnauthorized(): void
    {
        $this->actingAs($this->anonymousUser)
            ->post(
                route(WorkOrdersController::STORE_NAME)
            )->assertUnauthorized();

        $this->actingAs($this->unauthorizedUser)
            ->post(
                route(WorkOrdersController::STORE_NAME)
            )->assertUnauthorized();
    }

    /**
     * @test
     */
    public function technicianCanAddClient(): void
    {
        $this->withoutExceptionHandling()
            ->actingAs($this->technicianUser)
            ->post(
                route(
                    WorkOrdersController::STORE_NAME
                ),
                [Client::COMPANY_NAME => self::COMPANY_NAME]
            )->assertRedirect();

        $this->assertDatabaseHas(
            Client::TABLE,
            [Client::COMPANY_NAME => self::COMPANY_NAME,]
        );
    }

    /**
     * @test
     */
    public function technicianCanAddClientWithPerson(): void
    {
        $company_name = self::COMPANY_NAME . uniqid('b', true);
        $person = factory(Person::class)->make();
        $this->withoutExceptionHandling()
            ->actingAs($this->technicianUser)
            ->post(
                route(WorkOrdersController::STORE_NAME),
                [
                    Client::COMPANY_NAME => $company_name,
                    Person::FIRST_NAME => $person->first_name,
                    Person::LAST_NAME => $person->last_name,
                ]
            )
            ->assertRedirect();
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::FIRST_NAME => $person->first_name,
                Person::LAST_NAME => $person->last_name,

            ]
        );
        $this->assertDatabaseHas(
            Client::TABLE,
            [
                Client::COMPANY_NAME => $company_name,
            ]
        );
    }

    /**
     * @test
     */
    public function authorizedUserCannotAddClientOnly(): void
    {
        $company_name = self::COMPANY_NAME . uniqid('c', true);
        $this->withExceptionHandling()
            ->actingAs($this->authorizedUser)
            ->post(
                route(WorkOrdersController::STORE_NAME),
                [
                    Client::COMPANY_NAME => $company_name,
                ]
            )->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseMissing(
            Client::TABLE,
            [
                Client::COMPANY_NAME => $company_name,
            ]
        );
    }

    /**
     * @test
     */
    public function authorizedUserCanAddClientWithPerson(): void
    {
        $company_name = self::COMPANY_NAME . uniqid('d', true);
        $person = factory(Person::class)->make();
        $this->actingAs($this->authorizedUser)
            ->post(
                route(WorkOrdersController::STORE_NAME),
                [
                    Client::COMPANY_NAME => $company_name,
                    Person::FIRST_NAME => $person->first_name,
                    Person::LAST_NAME => $person->last_name,
                ]
            )->assertRedirect();
        $this->assertDatabaseHas(
            Client::TABLE,
            [
                Client::COMPANY_NAME => $company_name,
            ]
        );
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::FIRST_NAME => $person->first_name,
                Person::LAST_NAME => $person->last_name,
            ]
        );
    }

    protected function setUp(): void
    {
        /** @var User $unauthorizedUser */
        /** @var User $authorizedUser */
        parent::setUp();
        $unauthorizedUser = factory(User::class)->create()->givePermissionTo(Permission::all());
        $unauthorizedUser->revokePermissionTo(WorkOrdersController::CREATE_NAME);
        $unauthorizedUser->revokePermissionTo(WorkOrdersController::STORE_NAME);
        $this->unauthorizedUser = $unauthorizedUser;
        $this->anonymousUser = factory(User::class)->make();
        $authorizedUser = factory(User::class)->create()->givePermissionTo(Permission::all());
        $authorizedUser->revokePermissionTo(UserPermissions::WORK_ORDER_OPTIONAL_PERSON);
        $this->authorizedUser = $authorizedUser;
        $this->technicianUser = factory(User::class)->create()->assignRole(UserRoles::TECHNICIAN);
    }
}
