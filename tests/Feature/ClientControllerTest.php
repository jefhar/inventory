<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\WorkOrders\Controllers\ClientController;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class ClientControllerTest
 *
 * @package Tests\Feature
 */
class ClientControllerTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function clientsShowPageListsWorkOrders(): void
    {
        /** @var Client $client */
        $client = factory(Client::class)->make();
        $client->company_name = "O'" . $client->company_name;
        $person = factory(Person::class)->make();
        $client->save();
        $client->person()->save($person);
        $this->withoutMix()
            ->actingAs($this->createEmployee())
            ->get(route(ClientController::SHOW_NAME, $client))
            ->assertSeeText($client->company_name);
    }
}
