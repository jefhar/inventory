<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\Support\Luhn;
use App\User;
use App\WorkOrders\Controllers\WorkOrdersController;
use App\WorkOrders\Requests\WorkOrderStoreRequest;
use App\WorkOrders\Requests\WorkOrderUpdateRequest;
use App\WorkOrders\Resources\WorkOrderResource;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
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
        $this->withoutMix()
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
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
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->post(
                route(WorkOrdersController::STORE_NAME),
                [WorkOrderStoreRequest::CLIENT_COMPANY_NAME => self::COMPANY_NAME]
            )
            ->assertJson(['created' => true,])
            ->assertCreated()->assertHeader(
                'Location',
                url(route(WorkOrdersController::SHOW_NAME, ['workorder' => Luhn::create(1)]))
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
        /** @var Person $person */
        $person = factory(Person::class)->make();
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->post(
                route(WorkOrdersController::STORE_NAME),
                [
                    WorkOrderStoreRequest::CLIENT_COMPANY_NAME => $company_name,
                    WorkOrderStoreRequest::CLIENT_FIRST_NAME => $person->first_name,
                    WorkOrderStoreRequest::CLIENT_LAST_NAME => $person->last_name,
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
        $this->withoutMix()
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
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
        /** @var Product $product */
        $product = factory(Product::class)->make();
        $product->manufacturer()->associate($manufacturer);

        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();

        /** @var Client $client */
        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $client->person()->save($person);
        $client->workOrders()->save($workOrder);
        $workOrder->products()->save($product);

        $this->withoutMix()
            ->withoutExceptionHandling()
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->get(route(WorkOrdersController::EDIT_NAME, $workOrder))
            ->assertOk()->assertSeeText('Edit Work Order')
            ->assertSeeText($product->manufacturer->name)
            ->assertSeeText($product->model);
    }

    /**
     * @test
     */
    public function canToggleLockedWorkOrder(): void
    {
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->make();
        $person = factory(Person::class)->make();
        $client = $workOrder->client;
        $client->person()->save($person);
        $workOrder->is_locked = false;
        $workOrder->save();
        $client->person()->save($person);
        $this
            ->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, ['workorder' => $workOrder]),
                [WorkOrderUpdateRequest::IS_LOCKED => true]
            )->assertJson(
                [
                    WorkOrderResource::ID => $workOrder->luhn,
                    WorkOrderResource::IS_LOCKED => true,
                ]
            )->assertJsonMissing(
                [
                    WorkOrderResource::CLIENT_COMPANY_NAME => '',
                    WorkOrderResource::EMAIL => '',
                    WorkOrderResource::FIRST_NAME => '',
                    WorkOrderResource::LAST_NAME => '',
                    WorkOrderResource::PHONE_NUMBER => '',
                    WorkOrderResource::INTAKE => '',
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
                [WorkOrderUpdateRequest::IS_LOCKED => false]
            )->assertJson(
                [
                    WorkOrderResource::ID => $workOrder->luhn,
                    WorkOrderResource::IS_LOCKED => false,
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
                    WorkOrderResource::CLIENT_COMPANY_NAME => $newClient->company_name,
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
                    WorkOrderUpdateRequest::CLIENT_COMPANY_NAME => $newClient->company_name,
                ]
            )
            ->assertDontSee(WorkOrderResource::EMAIL)
            ->assertDontSee(WorkOrderResource::FIRST_NAME)
            ->assertDontSee(WorkOrderResource::LAST_NAME)
            ->assertDontSee(WorkOrderResource::PHONE_NUMBER)
            ->assertDontSee(WorkOrderResource::INTAKE)
            ->assertOk()
            ->assertSee(WorkOrderResource::CLIENT_COMPANY_NAME);
    }

    /**
     * @test
     */
    public function updateOnlyReturnsWhatIsSent(): void
    {
        $faker = Factory::create();
        /** @var Client $newClient */
        $newClient = factory(Client::class)->make();
        /** @var Person $newPerson */
        $newPerson = factory(Person::class)->make();
        $workOrder = factory(WorkOrder::class)->create();
        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [WorkOrderUpdateRequest::CLIENT_COMPANY_NAME => $newClient->company_name,]
            )
            ->assertDontSee(WorkOrderResource::EMAIL)
            ->assertDontSee(WorkOrderResource::FIRST_NAME)
            ->assertDontSee(WorkOrderResource::LAST_NAME)
            ->assertDontSee(WorkOrderResource::PHONE_NUMBER)
            ->assertDontSee(WorkOrderResource::INTAKE)
            ->assertOk()
            ->assertSee(WorkOrderResource::CLIENT_COMPANY_NAME);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    WorkOrderUpdateRequest::CLIENT_COMPANY_NAME => $newClient->company_name,
                    WorkOrderUpdateRequest::FIRST_NAME => $newPerson->first_name,
                ]
            )
            ->assertDontSee(WorkOrderResource::EMAIL)
            ->assertDontSee(WorkOrderResource::LAST_NAME)
            ->assertDontSee(WorkOrderResource::PHONE_NUMBER)
            ->assertDontSee(WorkOrderResource::INTAKE)
            ->assertOk()
            ->assertSee(WorkOrderResource::CLIENT_COMPANY_NAME)
            ->assertSee(WorkOrderResource::FIRST_NAME);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    WorkOrderUpdateRequest::CLIENT_COMPANY_NAME => $newClient->company_name,
                    WorkOrderUpdateRequest::LAST_NAME => $newPerson->last_name,
                ]
            )
            ->assertDontSee(WorkOrderResource::EMAIL)
            ->assertDontSee(WorkOrderResource::FIRST_NAME)
            ->assertDontSee(WorkOrderResource::PHONE_NUMBER)
            ->assertDontSee(WorkOrderResource::INTAKE)
            ->assertOk()
            ->assertSee(WorkOrderResource::CLIENT_COMPANY_NAME)
            ->assertSee(WorkOrderResource::LAST_NAME);

        $this->actingAs($this->createEmployee(UserRoles::TECHNICIAN))
            ->patch(
                route(WorkOrdersController::UPDATE_NAME, $workOrder),
                [
                    WorkOrderUpdateRequest::EMAIL => $newPerson->email,
                    WorkOrderUpdateRequest::PHONE_NUMBER => $newClient->company_name,
                    WorkOrderUpdateRequest::INTAKE => $faker->text(),
                ]
            )
            ->assertDontSee(WorkOrderResource::CLIENT_COMPANY_NAME)
            ->assertDontSee(WorkOrderResource::FIRST_NAME)
            ->assertDontSee(WorkOrderResource::LAST_NAME)
            ->assertOk()
            ->assertSee(WorkOrderResource::EMAIL)
            ->assertSee(WorkOrderResource::PHONE_NUMBER)
            ->assertSee(WorkOrderResource::INTAKE);
    }
}
