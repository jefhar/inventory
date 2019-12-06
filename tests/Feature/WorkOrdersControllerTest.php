<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\User;
use App\WorkOrders\Controllers\WorkOrdersController;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Faker\Generator as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class WorkOrdersControllerTest
 *
 * @package Tests\Feature
 */
class WorkOrdersControllerTest extends TestCase
{
    use RefreshDatabase;

    private const COMPANY_NAME = 'George Q. Client';
    private User $transient;
    private User $user;

    /**
     * @test
     */
    public function guestCreateIsUnauthorized(): void
    {
        $this->get(
            route(WorkOrdersController::CREATE_NAME)
        )->assertRedirect('/login');
    }

    /**
     * @test
     * @SE-20 Testing that a locked user cannot access pages.
     */
    public function lockedUserIsUnauthorized(): void
    {
        factory(User::class)->create();
        $this->get(
            route(WorkOrdersController::CREATE_NAME)
        )->assertRedirect('/login');
        $workOrder = factory(WorkOrder::class)->create();

        $this->get(
            route(WorkOrdersController::SHOW_NAME, $workOrder)
        )->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function techUserCreateIsOk(): void
    {
        $this->withExceptionHandling()->actingAs($this->user)
            ->get(
                route(WorkOrdersController::CREATE_NAME)
            )->assertOk();
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
        $this->actingAs($this->user)
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
            ->actingAs($this->user)
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
    public function technicianIndexIsOk(): void
    {
        $this->actingAs($this->user)->withoutExceptionHandling()
            ->get(route(WorkOrdersController::INDEX_NAME))
            ->assertOk()
            ->assertSeeText('Work Orders');
    }

    /**
     * @test
     */
    public function editPageExists(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $client->person()->save($person);
        $client->workOrders()->save($workOrder);
        $this->actingAs($this->user)->withoutExceptionHandling()
            ->get(route(WorkOrdersController::EDIT_NAME, $workOrder))
            ->assertOk()->assertSeeText('Edit Work Order');
    }

    /**
     * @test
     */
    public function canToggleLockedWorkOrder(): void
    {
        $workOrder = factory(WorkOrder::class)->make();
        $workOrder->is_locked = false;
        $workOrder->save();
        $this
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, ['workorder' => $workOrder]),
                [WorkOrder::IS_LOCKED => true]
            )->assertJson(
                [
                    WorkOrder::ID => $workOrder->id,
                    WorkOrder::IS_LOCKED => true,
                ]
            )->assertOk();
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => true,
            ]
        );

        $this
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, ['workorder' => $workOrder]),
                [WorkOrder::IS_LOCKED => false]
            )->assertJson(
                [
                    WorkOrder::ID => $workOrder->id,
                    WorkOrder::IS_LOCKED => false,
                ]
            )->assertOk();
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => false,
            ]
        );
    }

    /**
     * @test
     */
    public function updateUpdatesCompanyName(): void
    {
        $newClient = factory(Client::class)->make();
        $workOrder = factory(WorkOrder::class)->create();
        $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                ]
            )->assertOk();
        $this->assertDatabaseHas(
            Client::TABLE,
            [
                Client::COMPANY_NAME => $newClient->company_name,
            ]
        );
    }

    /**
     * @test
     */
    public function updateCompanyNameOnlyReturnsCompanyName(): void
    {
        $newClient = factory(Client::class)->make();
        $workOrder = factory(WorkOrder::class)->create();
        $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                ]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::FIRST_NAME)
            ->assertDontSee(Person::LAST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME);
    }

    /**
     * @test
     */
    public function updateOnlyReturnsWhatIsSent(Faker $faker): void
    {
        $newClient = factory(Client::class)->make();
        $workOrder = factory(WorkOrder::class)->create();
        $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                ]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::FIRST_NAME)
            ->assertDontSee(Person::LAST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME);

        $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                    Person::FIRST_NAME => $newClient->person->first_name,
                ]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::LAST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME)
            ->assertSee(Person::FIRST_NAME);

        $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                    Person::LAST_NAME => $newClient->person->last_name,
                ]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::FIRST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME)
            ->assertSee(Person::LAST_NAME);

        $this->withoutExceptionHandling()
            ->actingAs($this->user)
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Person::EMAIL => $newClient->email,
                    Person::PHONE_NUMBER => $newClient->company_name,
                    WorkOrder::INTAKE => $faker->text(),
                ]
            )
            ->assertDontSee(Client::COMPANY_NAME)
            ->assertDontSee(Person::FIRST_NAME)
            ->assertDontSee(Person::LAST_NAME)
            ->assertOk()
            ->assertSee(Person::EMAIL)
            ->assertSee(Person::PHONE_NUMBER)
            ->assertSee(WorkOrder::INTAKE);
    }

    protected function setUp(): void
    {
        /** @var User $guestUser */
        /** @var User $authorizedUser */
        parent::setUp();
        $this->transient = factory(User::class)->make();
        $this->user = factory(User::class)->create()->assignRole(UserRoles::TECHNICIAN);
    }
}
