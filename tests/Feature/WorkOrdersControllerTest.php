<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\User;
use App\WorkOrders\Controllers\WorkOrdersController;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Tdely\Luhn\Luhn;
use Tests\TestCase;
use Tests\Traits\FullUsers;

/**
 * Class WorkOrdersControllerTest
 *
 * @package Tests\Feature
 */
class WorkOrdersControllerTest extends TestCase
{
    use FullUsers;

    private const COMPANY_NAME = 'George Q. Client';

    /**
     * @test
     */
    public function guestCreateIsUnauthorized(): void
    {
        $this->get(route(WorkOrdersController::CREATE_NAME))
            ->assertRedirect('/login');
    }

    /**
     * @test
     * @SE-20 Testing that a locked user cannot access pages.
     */
    public function userWithNoRolesIsUnauthorized(): void
    {
        $this->actingAs(factory(User::class)->create())
            ->get(route(WorkOrdersController::CREATE_NAME))
            ->assertForbidden();

        $workOrder = factory(WorkOrder::class)->create();
        $this->get(route(WorkOrdersController::SHOW_NAME, $workOrder))
            ->assertForbidden();
    }

    /**
     * @test
     */
    public function techUserCreateIsOk(): void
    {
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(WorkOrdersController::CREATE_NAME))
            ->assertOk();
    }

    /**
     * @test
     */
    public function guestStoreIsRedirectToLogin(): void
    {
        $this->post(route(WorkOrdersController::STORE_NAME))
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function technicianCanStoreWorkOrderWithCompanyNameOnly(): void
    {
        $checksum = Luhn::create(1);
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->post(
                route(WorkOrdersController::STORE_NAME),
                [Client::COMPANY_NAME => self::COMPANY_NAME]
            )
            ->assertJson(['created' => true,])
            ->assertCreated()->assertHeader(
                'Location',
                url(route(WorkOrdersController::SHOW_NAME, ['workorder' => $checksum]))
            );

        $this->assertDatabaseHas(
            Client::TABLE,
            [Client::COMPANY_NAME => self::COMPANY_NAME]
        );
    }

    /**
     * @test
     */
    public function technicianCanStoreWorkOrderWithCompanyNameAndPerson(): void
    {
        $company_name = self::COMPANY_NAME . uniqid('b', true);
        $person = factory(Person::class)->make();
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
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
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(WorkOrdersController::INDEX_NAME))
            ->assertOk()
            ->assertSeeText('Work Orders');
    }

    /**
     * @test
     */
    public function editPageExists(): void
    {
        $manufacturer = factory(Manufacturer::class)->create();
        $product = factory(Product::class)->make();
        $product->manufacturer()->associate($manufacturer);

        $workOrder = factory(WorkOrder::class)->create();

        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $client->person()->save($person);
        $client->workOrders()->save($workOrder);
        $workOrder->products()->save($product);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(WorkOrdersController::EDIT_NAME, $workOrder))
            ->assertOk()->assertSeeText('Edit Work Order')
            ->assertSeeText(htmlspecialchars($product->manufacturer->name, ENT_QUOTES | ENT_HTML401))
            ->assertSeeText($product->model);
    }

    /**
     * @test
     */
    public function canToggleLockedWorkOrder(): void
    {
        $workOrder = factory(WorkOrder::class)->make();
        $person = factory(Person::class)->make();
        /** @var Client $client */
        $client = $workOrder->client;
        $client->person()->save($person);
        $workOrder->is_locked = false;
        $workOrder->save();
        $client->person()->save($person);
        $this
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, ['workorder' => $workOrder]),
                [WorkOrder::IS_LOCKED => true]
            )->assertJson(
                [
                    WorkOrder::ID => $workOrder->luhn,
                    WorkOrder::IS_LOCKED => true,
                ]
            )->assertJsonMissing(
                [
                    Client::COMPANY_NAME => '',
                    Person::EMAIL => '',
                    Person::FIRST_NAME => '',
                    Person::LAST_NAME => '',
                    Person::PHONE_NUMBER => '',
                    WorkOrder::INTAKE => '',
                ]
            )
            ->assertOk();
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => true,
            ]
        );

        $this
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, ['workorder' => $workOrder]),
                [WorkOrder::IS_LOCKED => false]
            )->assertJson(
                [
                    WorkOrder::ID => $workOrder->luhn,
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
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
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
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
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
    public function updateOnlyReturnsWhatIsSent(): void
    {
        $faker = Factory::create();
        $newClient = factory(Client::class)->make();
        $newPerson = factory(Person::class)->make();
        $workOrder = factory(WorkOrder::class)->create();
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [Client::COMPANY_NAME => $newClient->company_name,]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::FIRST_NAME)
            ->assertDontSee(Person::LAST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                    Person::FIRST_NAME => $newPerson->first_name,
                ]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::LAST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME)
            ->assertSee(Person::FIRST_NAME);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Client::COMPANY_NAME => $newClient->company_name,
                    Person::LAST_NAME => $newPerson->last_name,
                ]
            )
            ->assertDontSee(Person::EMAIL)
            ->assertDontSee(Person::FIRST_NAME)
            ->assertDontSee(Person::PHONE_NUMBER)
            ->assertDontSee(WorkOrder::INTAKE)
            ->assertOk()
            ->assertSee(Client::COMPANY_NAME)
            ->assertSee(Person::LAST_NAME);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    Person::EMAIL => $newPerson->email,
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
}
