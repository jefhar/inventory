<?php

/**
 * Copyright (c) 2018, 2019 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Controllers\AjaxSearchController;
use App\Admin\Permissions\UserRoles;
use App\User;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AjaxSearchTest extends TestCase
{
    private User $unauthorizedUser;
    private User $authorizedUser;

    public function setUp(): void
    {
        /** @var User $authorizedUser */
        parent::setUp();
        $unauthorizedUser = factory(User::class)->create();
        $authorizedUser = factory(User::class)->create();
        $authorizedUser->assignRole(UserRoles::EMPLOYEE);
        $this->unauthorizedUser = $unauthorizedUser;
        $this->authorizedUser = $authorizedUser;
    }

    /**
     * @test
     */
    public function anonymousIsUnauthorized(): void
    {
        $this->get(route(AjaxSearchController::SHOW_NAME, ['field' => Client::COMPANY_NAME]))->assertUnauthorized();
    }

    /**
     * @test
     */
    public function unauthorizedIsUnauthorized(): void
    {
        $this->withExceptionHandling()->actingAs($this->unauthorizedUser);
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                [
                    'field' => Client::COMPANY_NAME,
                ]
            )
        )
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function authorizedIsOK(): void
    {
        $this->actingAs($this->authorizedUser)->withoutExceptionHandling();
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                ['field' => Client::COMPANY_NAME]
            )
        )->assertOK();
    }

    /**
     * @test
     */
    public function knownFieldIsOK(): void
    {
        $this->actingAs($this->authorizedUser);
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                [
                    'field' => Client::COMPANY_NAME,
                ]
            )
        )->assertOk();
    }

    /**
     * @test
     */
    public function unknownFieldIsBad()
    {
        $this->actingAs($this->authorizedUser)
            ->get(route(AjaxSearchController::SHOW_NAME, ['field' => 'flarp']))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     */
    public function clientCompanyNameSearchReturnsJson(): void
    {
        $company_name = 'John';
        for ($i = 0; $i < 50; $i++) {
            $client = factory(Client::class)->create();
            $person = factory(Person::class)->make();
            $client->person()->save($person);
        }
        $client = factory(Client::class)->create();
        $client->company_name = $company_name . uniqid('4', false);
        $client->save();
        $person = factory(Person::class)->make();
        $client->person()->save($person);

        $red_herring_client = factory(Client::class)->create();
        $red_herring_client->company_name = $company_name . uniqid('e', false);
        $red_herring_client->save();
        $red_herring_person = factory(Person::class)->make();
        $red_herring_client->person()->save($red_herring_person);

        $this->actingAs($this->authorizedUser)
            ->get(
                route(AjaxSearchController::SHOW_NAME, ['field' => Client::COMPANY_NAME, 'q' => 'J',])
            )->assertJsonFragment(
                [
                    Client::COMPANY_NAME => $client->company_name,
                    Person::FIRST_NAME => $client->person->first_name,
                    Person::LAST_NAME => $client->person->last_name,
                ]
            );
    }

}
