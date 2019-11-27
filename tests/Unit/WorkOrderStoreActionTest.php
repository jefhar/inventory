<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\DataTransferObjects\ClientObject;
use App\Admin\DataTransferObjects\PersonObject;
use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Tests\TestCase;

class WorkOrderStoreActionTest extends TestCase
{
    private const COMPANY_NAME = 'George Q. Client';
    private ClientObject $clientObject;
    private PersonObject $personObject;
    private User $user;

    /**
     * @test
     */
    public function workOrderStoreActionAddsCompanyNameToStorage(): void
    {
        $this->actingAs($this->user);
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
        $this->actingAs($this->user);
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
        $this->actingAs($this->user);
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
        $authorizedUser = factory(User::class)->create();
        $authorizedUser->assignRole(UserRoles::EMPLOYEE);
        $this->actingAs($this->user);

        $client = factory(Client::class)->create();
        $person = factory(Person::class)->make();
        $client->person()->save($person);

        $clientObject = new ClientObject(
            [
                Client::COMPANY_NAME => $client->company_name,
            ]
        );
        $personObject = new PersonObject(
            [
                Person::FIRST_NAME => $person->first_name,
                Person::LAST_NAME => $person->last_name,
                Person::EMAIL => $person->email,
                Person::PHONE_NUMBER => $person->phone_number,
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
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::EMPLOYEE);
        $user->save();
        $this->user = $user;
        $person = factory(Person::class)->make();

        $this->clientObject = new ClientObject(
            [Client::COMPANY_NAME => self::COMPANY_NAME],
        );
        $this->personObject = PersonObject::fromRequest(
            [
                Person::EMAIL => $person->email,
                Person::FIRST_NAME => $person->first_name,
                Person::LAST_NAME => $person->last_name,
                Person::PHONE_NUMBER => $person->phone_number,
            ]
        );
    }
}
