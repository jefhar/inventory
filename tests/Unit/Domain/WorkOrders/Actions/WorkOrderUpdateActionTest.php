<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\WorkOrders\Actions;

use App\Admin\Permissions\UserPermissions;
use App\User;
use App\WorkOrders\DataTransferObjects\WorkOrderUpdateObject;
use Domain\WorkOrders\Actions\WorkOrdersUpdateAction;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Tests\TestCase;

class WorkOrderUpdateActionTest extends TestCase
{
    private User $user;

    /**
     * @test
     */
    public function togglesIsLocked(): void
    {
        $workOrder = factory(WorkOrder::class)->make();
        $workOrder->is_locked = false;
        $workOrder->save();
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => false,
            ]
        );
        $workOrderUpdateObjectLocked = WorkOrderUpdateObject::fromRequest(
            [
                WorkOrder::IS_LOCKED => true,
            ]
        );
        $workOrderUpdateObjectUnLocked = WorkOrderUpdateObject::fromRequest(
            [
                WorkOrder::IS_LOCKED => false,
            ]
        );

        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObjectLocked);
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [
                WorkOrder::ID => $workOrder->id,
                WorkOrder::IS_LOCKED => true,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObjectUnLocked);
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
        $workOrder = factory(WorkOrder::class)->create();
        $newClient = factory(Client::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Client::COMPANY_NAME => $newClient->company_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(
            Client::TABLE,
            [
                Client::COMPANY_NAME => $workOrderUpdateObject->company_name,
            ]
        );
    }

    /**
     * @test
     */
    public function updateUpdatesNewPersonFirstName(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $person = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::FIRST_NAME => $person->first_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::FIRST_NAME => $workOrderUpdateObject->first_name]);
    }

    /**
     * @test
     */
    public function updateUpdatesExistingPersonFirstName(): void
    {
        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $workOrder = factory(WorkOrder::class)->make();
        $client->person()->save($person);
        $workOrder->client()->associate($client);
        $client->push();
        $newPerson = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::FIRST_NAME => $newPerson->first_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::FIRST_NAME => $workOrderUpdateObject->first_name]);
    }

    /**
     * @test
     */
    public function updateUpdatesNewPersonLastName(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $person = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::LAST_NAME => $person->last_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::LAST_NAME => $workOrderUpdateObject->last_name]);
    }

    /**
     * @test
     */
    public function updateUpdatesExistingPersonLastName(): void
    {
        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $workOrder = factory(WorkOrder::class)->make();
        $client->person()->save($person);
        $workOrder->client()->associate($client);
        $client->push();
        $newPerson = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::LAST_NAME => $newPerson->last_name,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::LAST_NAME => $workOrderUpdateObject->last_name]);
    }

    /**
     * @test
     */
    public function updateUpdatesNewPersonEmail(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $person = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::EMAIL => $person->email,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::EMAIL => $workOrderUpdateObject->email]);
    }

    /**
     * @test
     */
    public function updateUpdatesExistingPersonEmail(): void
    {
        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $workOrder = factory(WorkOrder::class)->make();
        $client->person()->save($person);
        $workOrder->client()->associate($client);
        $client->push();
        $newPerson = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::EMAIL => $newPerson->email,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(Person::TABLE, [Person::EMAIL => $workOrderUpdateObject->email]);
    }

    /**
     * @test
     */
    public function updateUpdatesNewPersonPhoneNumber(): void
    {
        $workOrder = factory(WorkOrder::class)->create();
        $person = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::PHONE_NUMBER => $person->phone_number,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::PHONE_NUMBER => Person::unformatPhoneNumber($workOrderUpdateObject->phone_number),
            ]
        );
    }

    /**
     * @test
     */
    public function updateUpdatesExistingPersonPhoneNumber(): void
    {
        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $workOrder = factory(WorkOrder::class)->make();
        $client->person()->save($person);
        $workOrder->client()->associate($client);
        $client->push();
        $newPerson = factory(Person::class)->make();
        $workOrderUpdateObject = WorkOrderUpdateObject::fromRequest(
            [
                Person::PHONE_NUMBER => $newPerson->phone_number,
            ]
        );
        WorkOrdersUpdateAction::execute($workOrder, $workOrderUpdateObject);
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::PHONE_NUMBER => Person::unformatPhoneNumber($workOrderUpdateObject->phone_number),
            ]
        );
    }

    public function setUp(): void
    {
        parent::setUp();
        $user = factory(User::class)->create();
        $user->givePermissionTo(UserPermissions::IS_EMPLOYEE);
        $this->user = $user;
    }
}
