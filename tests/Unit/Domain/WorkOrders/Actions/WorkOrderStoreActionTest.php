<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit\Domain\WorkOrders\Actions;

use App\Admin\Permissions\UserRoles;
use App\User;
use App\WorkOrders\DataTransferObjects\ClientObject;
use App\WorkOrders\DataTransferObjects\PersonObject;
use App\WorkOrders\Requests\WorkOrderStoreRequest;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class WorkOrderStoreActionTest
 *
 * @package Tests\Unit\Domain\WorkOrders\Actions
 */
class WorkOrderStoreActionTest extends TestCase
{
    use FullObjects;

    private const COMPANY_NAME = 'George Q. Client';
    private ClientObject $clientObject;
    private PersonObject $personObject;
    private User $employee;

    /**
     * @test
     */
    public function workOrderStoreActionAddsCompanyNameToStorage(): void
    {
        WorkOrdersStoreAction::execute($this->clientObject, $this->personObject);
        $this->assertDatabaseHas(
            Client::TABLE,
            [
                Client::COMPANY_NAME => self::COMPANY_NAME,
            ]
        );
    }

    /**
     * @test
     */
    public function workOrderStoreActionAddsGivenPersonToClient(): void
    {
        WorkOrdersStoreAction::execute($this->clientObject, $this->personObject);
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::FIRST_NAME => $this->personObject->first_name,
                Person::LAST_NAME => $this->personObject->last_name,
                Person::PHONE_NUMBER => Person::unformatPhoneNumber($this->personObject->phone_number),
                Person::EMAIL => $this->personObject->email,
            ]
        );
        $client = Client::find(1);
        $this->assertEquals($this->personObject->first_name, $client->person->first_name);
        $this->assertEquals($this->personObject->last_name, $client->person->last_name);
    }

    /**
     * @test
     */
    public function workOrderStoreWithoutPersonCreatesBlankPerson(): void
    {
        $clientObject = $this->clientObject;
        $personObject = PersonObject::fromRequest([]);
        WorkOrdersStoreAction::execute($clientObject, $personObject);

        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::ID => 1,
                Person::PHONE_NUMBER => '0000000000',
            ]
        );
    }

    /**
     * @test
     */
    public function storingWorkOrderStoresWorkOrderSuccessfullyIfClientAlreadyExists(): void
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();
        /** @var Person $person */
        $person = factory(Person::class)->make();
        $client->person()->save($person);

        $clientObject = ClientObject::fromRequest(
            [
                ClientObject::CLIENT_COMPANY_NAME => $client->company_name,
            ]
        );
        $personObject = PersonObject::fromRequest(
            [
                PersonObject::FIRST_NAME => $person->first_name,
                PersonObject::LAST_NAME => $person->last_name,
                PersonObject::EMAIL => $person->email,
                PersonObject::PHONE_NUMBER => $person->phone_number,
            ]
        );
        WorkOrdersStoreAction::execute(
            $clientObject,
            $personObject
        );
        $this->assertDatabaseHas(
            WorkOrder::TABLE,
            [WorkOrder::CLIENT_ID => $client->id]
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createEmployee(UserRoles::EMPLOYEE));
        $this->clientObject = ClientObject::fromRequest(
            [ClientObject::CLIENT_COMPANY_NAME => self::COMPANY_NAME],
        );

        /** @var Person $person */
        $person = factory(Person::class)->make();
        $this->personObject = PersonObject::fromRequest(
            [
                WorkOrderStoreRequest::EMAIL => $person->email,
                WorkOrderStoreRequest::FIRST_NAME => $person->first_name,
                WorkOrderStoreRequest::LAST_NAME => $person->last_name,
                WorkOrderStoreRequest::PHONE_NUMBER => $person->phone_number,
            ]
        );
    }
}
