<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use App\AjaxSearch\DataTransferObjects\AjaxSearchObject;
use App\AjaxSearch\Requests\AjaxSearchRequest;
use Domain\AjaxSearch\Actions\AjaxSearchAction;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Tests\TestCase;

/**
 * Class AjaxSearchActionTest
 *
 * @package Tests\Unit
 */
class AjaxSearchActionTest extends TestCase
{
    /**
     * @test
     */
    public function searchActionReturnsACollection(): void
    {
        for ($i = 0; $i < 50; $i++) {
            /** @var Client $client */
            $client = factory(Client::class)->create();
            $person = factory(Person::class)->make();
            $client->person()->save($person);
            $client->save();
        }

        // Ensure that 2 companies contain 'John'
        $redHerringCompanyName = 'John ' . uniqid('A', false);
        $redHerringClient = new Client();
        $redHerringPerson = new Person(
            [
                Person::FIRST_NAME => 'Redd',
                Person::LAST_NAME => 'Herring',
                Person::EMAIL => 'email@example.com',
                Person::PHONE_NUMBER => '12345',
            ]
        );
        $redHerringClient->company_name = $redHerringCompanyName;
        $redHerringClient->save();
        $redHerringClient->person()->save($redHerringPerson);

        $companyName = 'John ' . uniqid('-', false);
        $client = factory(Client::class)->make();
        $person = factory(Person::class)->make();
        $client->company_name = $companyName;
        $client->save();
        $client->person()->save($person);

        $options = AjaxSearchAction::findBy(
            AjaxSearchObject::fromRequest(AjaxSearchRequest::SEARCH_COMPANY_NAME, ['q' => 'j'])
        );

        $this->assertContains($companyName, $options->pluck(Client::COMPANY_NAME));
    }

    /**
     * @test
     */
    public function ajaxSearchIndexFindsCompanyName(): void
    {
        factory(Client::class, 50)->create();
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $options = AjaxSearchAction::findAll(substr($client->company_name, 0, 2));
        $this->assertStringContainsString($client->company_name, $options->toJson());
    }

    /**
     * @test
     */
    public function ajaxSearchIndexFindsLastName(): void
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $people = factory(Person::class, 50)->make([Person::CLIENT_ID => 1]);
        $client->person()->saveMany($people);

        /** @var Person $person */
        $person = factory(Person::class)->create([Person::CLIENT_ID => 1]);
        $options = AjaxSearchAction::findAll(substr($person->last_name, 0, 2));
        $this->assertStringContainsString($person->last_name, $options->toJson());
    }

    /**
     * @test
     */
    public function ajaxSearchIndexFindsManufacturerName(): void
    {
        factory(Manufacturer::class, 50)->create();
        /** @var Manufacturer $manufacturer */
        $manufacturer = factory(Manufacturer::class)->create();
        $options = AjaxSearchAction::findBy(
            AjaxSearchObject::fromRequest(
                AjaxSearchRequest::SEARCH_MANUFACTURER,
                ['q' => substr($manufacturer->name, 0, 2)]
            )
        );
        $this->assertStringContainsString($manufacturer->name, $options->toJson());
    }

    /**
     * @test
     */
    public function ajaxSearchIndexFindsModelName(): void
    {
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        factory(Product::class, 50)->create([Product::WORK_ORDER_ID => $workOrder->id]);
        /** @var Product $product */
        $product = factory(Product::class)->create([Product::WORK_ORDER_ID => $workOrder->id]);

        $options = AjaxSearchAction::findBy(
            AjaxSearchObject::fromRequest(
                AjaxSearchRequest::SEARCH_MODEL,
                ['q' => substr($product->model, 0, 2)]
            )
        );
        $this->assertStringContainsString($product->model, $options->toJson());
    }
}
