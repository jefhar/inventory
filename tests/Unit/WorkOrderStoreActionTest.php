<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Controllers\WorkOrdersController;
use App\Admin\DataTransferObjects\ClientObject;
use App\Admin\DataTransferObjects\PersonObject;
use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\WorkOrders\Actions\WorkOrdersStoreAction;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Domain\WorkOrders\WorkOrder;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class WorkOrderStoreActionTest extends TestCase
{
    private const COMPANY_NAME = 'George Q. Client';
    private ClientObject $clientObject;
    private PersonObject $personObject;

    /**
     * @test
     */
    public function workOrderAddsCompanyNameToStorage(): void
    {
        $user = factory(User::class)->create();
        $user->givePermissionTo(WorkOrdersController::STORE_NAME);
        $this->actingAs($user);
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
    public function workOrderAddsPersonToClient(): void
    {
        $user = factory(User::class)->create();
        $user->givePermissionTo(WorkOrdersController::STORE_NAME);
        $this->actingAs($user);
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
    public function clientPersonIsOptionalForTech()
    {
        $clientName = self::COMPANY_NAME . uniqid('a', false);
        $this->actingAs(factory(User::class)->create()->assignRole(UserRoles::TECHNICIAN));

        WorkOrdersStoreAction::execute(
            new ClientObject([Client::COMPANY_NAME => $clientName]),
            null
        );
        $this->assertDatabaseHas(
            Client::TABLE,
            [Client::COMPANY_NAME => $clientName,]
        );

        $clientWithPerson = self::COMPANY_NAME . uniqid('b', false);
        WorkOrdersStoreAction::execute(
            new ClientObject([Client::COMPANY_NAME => $clientWithPerson]),
            $this->personObject
        );
        $this->assertDatabaseHas(
            Client::TABLE,
            [Client::COMPANY_NAME => $clientWithPerson]
        );
        $this->assertDatabaseHas(
            Person::TABLE,
            [
                Person::FIRST_NAME => $this->personObject->first_name,
                Person::LAST_NAME => $this->personObject->last_name,
            ]
        );
        /** @var Client $storedClientWithPerson */
        $storedClientWithPerson = Client::where(Client::COMPANY_NAME, $clientWithPerson)->first();
        $this->assertEquals($storedClientWithPerson->person->first_name, $this->personObject->first_name);
        $this->assertEquals($storedClientWithPerson->person->last_name, $this->personObject->last_name);
    }

    protected function setUp(): void
    {
        parent::setUp();
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

    /**
     * @test
     */
    public function personIsNotOptionalIfNotATech(): void
    {
        $this->withoutExceptionHandling()->actingAs(factory(User::class)->create()->assignRole(UserRoles::SALES_REP));
        $this->expectException(HttpException::class);
        WorkOrdersStoreAction::execute($this->clientObject);
    }

    /**
     * @test
     */
    public function storingWorkOrderStoresWorkOrderClientAlreadyExists(): void
    {
        $authorizedUser = factory(User::class)->create();
        $authorizedUser->givePermissionTo(WorkOrdersController::STORE_NAME);
        $this->actingAs($authorizedUser);

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
}
