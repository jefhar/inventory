<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Unit;

use Domain\AjaxSearch\Actions\AjaxSearch;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Tests\TestCase;

class AjaxSearchActionTest extends TestCase
{
    /**
     * @test
     */
    public function searchActionReturnsSomething(): void
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
                'first_name' => 'Redd',
                'last_name' => 'Herring',
                'email' => 'email@example.com',
                'phone_number' => '12345',
            ]
        );
        $red_herring_client->company_name = $red_herring_company_name;
        $red_herring_client->save();
        $red_herring_client->person()->save($red_herring_person);

        $company_name = 'John ' . uniqid('-', false);
        $client = new Client();
        $person = factory(Person::class)->make();
        $client->company_name = $company_name;
        $client->save();
        $client->person()->save($person);

        $options = AjaxSearch::findBy(Client::COMPANY_NAME, 'J');
        // dd($options->pluck(Client::COMPANY_NAME));

        $this->assertContains($company_name, $options->pluck(Client::COMPANY_NAME));
    }
}
