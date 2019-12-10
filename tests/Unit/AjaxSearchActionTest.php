<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\AjaxSearch\Actions\AjaxSearchAction;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
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
            $client = factory(Client::class)->create();
            $person = factory(Person::class)->make();
            $client->person()->save($person);
            $client->save();
        }

        // Ensure that 2 companies contain 'John'
        $red_herring_company_name = 'John ' . uniqid('A', false);
        $red_herring_client = new Client();
        $red_herring_person = new Person(
            [
                Person::FIRST_NAME => 'Redd',
                Person::LAST_NAME => 'Herring',
                Person::EMAIL => 'email@example.com',
                Person::PHONE_NUMBER => '12345',
            ]
        );
        $red_herring_client->company_name = $red_herring_company_name;
        $red_herring_client->save();
        $red_herring_client->person()->save($red_herring_person);

        $company_name = 'John ' . uniqid('-', false);
        $client = factory(Client::class)->make();
        $person = factory(Person::class)->make();
        $client->company_name = $company_name;
        $client->save();
        $client->person()->save($person);

        $options = AjaxSearchAction::findBy(Client::COMPANY_NAME, 'J');
        // dd($options->pluck(Client::COMPANY_NAME));

        $this->assertContains($company_name, $options->pluck(Client::COMPANY_NAME));
    }

    /**
     * @test
     */
    public function ajaxSearchIndexFindsCompanyName(): void
    {
        factory(Client::class, 50)->create();
        $client = factory(Client::class)->create();
        $options = AjaxSearchAction::findAll(substr($client->company_name, 0, 2));
        $this->assertStringContainsString($client->company_name, $options->toJson());
    }

    /**
     * @test
     */
    public function ajaxSearchIndexFindsLastName(): void
    {
        $client = factory(Client::class)->create();
        $people = factory(Person::class, 50)->make([Person::CLIENT_ID => 1]);
        $client->person()->saveMany($people);

        $person = factory(Person::class)->create([Person::CLIENT_ID => 1]);
        $options = AjaxSearchAction::findAll(substr($person->last_name, 0, 2));
        $this->assertStringContainsString($person->last_name, $options->toJson());
    }
}
