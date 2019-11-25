<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Controllers\WorkOrdersController;
use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkOrdersControllerTest extends TestCase
{
    use RefreshDatabase;

    private const COMPANY_NAME = 'George Q. Client';
    private User $guestUser;
    private User $technicianUser;

    /**
     * @test
     */
    public function guestCreateIsUnAuthorized(): void
    {
        $this->get(
            route(WorkOrdersController::CREATE_NAME)
        )->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function techUserCreateIsOk(): void
    {
        $this->withExceptionHandling()->actingAs($this->technicianUser)
            ->get(
                route(WorkOrdersController::CREATE_NAME)
            )->assertOK();
    }

    /**
     * @test
     */
    public function guestStoreIsRedirectToLogin(): void
    {
        $this
            ->post(
                route(WorkOrdersController::STORE_NAME)
            )->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function technicianCanStoreWorkOrderWithCompanyNameOnly(): void
    {
        $this->actingAs($this->technicianUser)
            ->post(
                route(
                    WorkOrdersController::STORE_NAME
                ),
                [Client::COMPANY_NAME => self::COMPANY_NAME]
            )
            ->assertJson(
                [
                    'created' => true,
                ]
            )
            ->assertCreated()->assertHeader(
                'Location',
                url(route(WorkOrdersController::SHOW_NAME, ['workorder' => 1]))
            );

        $this->assertDatabaseHas(
            Client::TABLE,
            [Client::COMPANY_NAME => self::COMPANY_NAME,]
        );
    }

    /**
     * @test
     */
    public function technicianCanStoreWorkOrderWithCompanyNameAndPerson(): void
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
            ->assertCreated();
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
    public function guestIndexIsUnauthorized(): void
    {
        $this->get(route(WorkOrdersController::INDEX_NAME))
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function technicianIndexIsOK(): void
    {
        $this->actingAs($this->technicianUser)->withoutExceptionHandling()
            ->get(route(WorkOrdersController::INDEX_NAME))
            ->assertOk()
            ->assertSeeText('Work Orders');
    }

    protected function setUp(): void
    {
        /** @var User $guestUser */
        /** @var User $authorizedUser */
        parent::setUp();
        $this->guestUser = factory(User::class)->make();
        $this->technicianUser = factory(User::class)->create()->assignRole(UserRoles::TECHNICIAN);
    }
}
