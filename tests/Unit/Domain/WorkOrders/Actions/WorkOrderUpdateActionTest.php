<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\WorkOrders\Actions;

use App\Admin\Permissions\UserPermissions;
use App\User;
use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use App\WorkOrders\DataTransferObjects\WorkOrderObject;
use Domain\WorkOrders\Actions\WorkOrdersUpdateAction;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class WorkOrderUpdateActionTest
 *
 * @package Tests\Unit\Domain\WorkOrders\Actions
 */
class WorkOrderUpdateActionTest extends TestCase
{
    use FullObjects;

    private User $user;

    /**
     * @test
     */
    public function togglesIsLocked(): void
    {
        $workOrder = $this->createFullWorkOrder();
        $workOrder->is_locked = false;
        $workOrder->save();
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => false,
            ]
        );
        $workOrderObjectLocked = WorkOrderObject::fromRequest(
            [
                WorkOrder::IS_LOCKED => true,
            ]
        );
        $workOrderObjectUnlocked = WorkOrderObject::fromRequest(
            [
                WorkOrder::IS_LOCKED => false,
            ]
        );
        $clientObject = ClientObject::fromRequest([]);
        $personObject = PersonObject::fromRequest([]);

        WorkOrdersUpdateAction::execute($workOrder, $workOrderObjectLocked, $clientObject, $personObject);
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => true,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderObjectUnlocked, $clientObject, $personObject);
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
        $workOrder = $this->createFullWorkOrder();
        /** @var Client $newClient */
        $newClient = factory(Client::class)->make();
        $workOrderObject = WorkOrderObject::fromRequest([]);
        $clientObject = ClientObject::fromRequest(
            [
                ClientObject::CLIENT_COMPANY_NAME => $newClient->company_name,
            ]
        );
        $personObject = PersonObject::fromRequest([]);

        WorkOrdersUpdateAction::execute($workOrder, $workOrderObject, $clientObject, $personObject);
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
    public function updateUpdatesPersonFirstName(): void
    {
        $workOrder = $this->createFullWorkOrder();
        /** @var Person $person */
        $person = factory(Person::class)->make();
        $workOrderObject = WorkOrderObject::fromRequest([]);
        $clientObject = ClientObject::fromRequest([]);
        $personObject = PersonObject::fromRequest(
            [
                PersonObject::FIRST_NAME => $person->first_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderObject, $clientObject, $personObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::FIRST_NAME => $person->first_name]);
    }

    /**
     * @test
     */
    public function updateUpdatesPersonLastName(): void
    {
        $workOrder = $this->createFullWorkOrder();
        /** @var Person $person */
        $person = factory(Person::class)->make();
        $workOrderObject = WorkOrderObject::fromRequest([]);
        $clientObject = ClientObject::fromRequest([]);
        $personObject = PersonObject::fromRequest(
            [
                PersonObject::LAST_NAME => $person->last_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderObject, $clientObject, $personObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::LAST_NAME => $person->last_name]);
    }

    /**
     * @test
     */
    public function updateUpdatesPersonEmail(): void
    {
        $workOrder = $this->createFullWorkOrder();
        /** @var Person $person */
        $person = factory(Person::class)->make();
        $workOrderObject = WorkOrderObject::fromRequest([]);
        $clientObject = ClientObject::fromRequest([]);
        $personObject = PersonObject::fromRequest(
            [
                PersonObject::EMAIL => $person->email,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderObject, $clientObject, $personObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::EMAIL => $person->email]);
    }

    /**
     * @test
     */
    public function updateUpdatesPersonPhoneNumber(): void
    {
        $workOrder = $this->createFullWorkOrder();
        /** @var Person $newPerson */
        $newPerson = factory(Person::class)->make();
        $workOrderObject = WorkOrderObject::fromRequest([]);
        $clientObject = ClientObject::fromRequest([]);
        $personObject = PersonObject::fromRequest(
            [
                PersonObject::PHONE_NUMBER => $newPerson->phone_number,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderObject, $clientObject, $personObject);
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::PHONE_NUMBER => Person::unformatPhoneNumber($newPerson->phone_number),
                Person::FIRST_NAME => $workOrder->client->person->first_name,
                Person::LAST_NAME => $workOrder->client->person->last_name,
                Person::EMAIL => $workOrder->client->person->email,
            ]
        );
    }

    public function setUp(): void
    {
        parent::setUp();
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $this->user = $user;
    }
}
