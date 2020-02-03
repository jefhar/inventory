<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\WorkOrders\Controllers\ClientsController;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Tests\TestCase;
use Tests\Traits\FullUsers;

/**
 * Class ClientsControllerTest
 *
 * @package Tests\Feature
 */
class ClientsControllerTest extends TestCase
{
    use FullUsers;

    /**
     * @test
     */
    public function clientsShowPageListsWorkOrders(): void
    {
        $client = factory(Client::class)->make();
        $client->company_name = "O'" . $client->company_name;
        $person = factory(Person::class)->make();
        $client->save();
        $client->person()->save($person);
        $this->actingAs($this->createEmployee())
            ->get(route(ClientsController::SHOW_NAME, $client))
            ->assertSeeText(htmlspecialchars($client->company_name, ENT_QUOTES | ENT_HTML401));
    }
}
