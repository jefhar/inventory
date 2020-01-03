<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\Admin\Permissions\UserRoles;
use App\AjaxSearch\Controllers\AjaxSearchController;
use App\Products\DataTransferObject\ProductStoreObject;
use App\User;
use Domain\Products\Actions\ProductStoreAction;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class AjaxSearchTest
 *
 * @package Tests\Feature
 */
class AjaxSearchTest extends TestCase
{
    private User $guest;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $guest = factory(User::class)->make();
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(UserRoles::EMPLOYEE);
        $this->guest = $guest;
        $this->user = $user;
    }

    /**
     * @test
     */
    public function anonymousIsUnauthorized(): void
    {
        $this->get(route(AjaxSearchController::SHOW_NAME, ['field' => Client::COMPANY_NAME]))->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function unauthorizedIsUnauthorized(): void
    {
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                [
                    'field' => Client::COMPANY_NAME,
                ]
            )
        )
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function authorizedIsOk(): void
    {
        $this->actingAs($this->user)->withoutExceptionHandling();
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                ['field' => Client::COMPANY_NAME]
            )
        )->assertOk();
    }

    /**
     * @test
     */
    public function knownFieldIsOk(): void
    {
        $this->actingAs($this->user);
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
    public function unknownFieldIsBad(): void
    {
        $this->actingAs($this->user)
            ->get(route(AjaxSearchController::SHOW_NAME, ['field' => 'flarp']))
            ->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
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

        $this->actingAs($this->user)
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

    /**
     * @test
     */
    public function indexReturnsSomething(): void
    {
        $client = factory(Client::class)->create();
        $this->actingAs($this->user)->withoutExceptionHandling()
            ->get(route(AjaxSearchController::INDEX_NAME, ['q' => $client->company_name]))
            ->assertOk()
            ->assertJsonFragment(['name' => $client->company_name]);
    }

    /**
     * @test
     */
    public function productSerialNumberSearchReturnsJson(): void
    {
        $faker = Factory::create();
        $workOrder = factory(WorkOrder::class)->create();
        for ($i = 0; $i < 15; $i++) {
            $serial = $faker->isbn13;
            $unsavedProduct = factory(Product::class)->make();
            $formRequest = [
                'values' => [
                    'radio-group-1575689472139' => $faker->word,
                    'select-1575689474390' => $faker->word,
                    'serial' => $serial,
                ],
                'manufacturer' => $unsavedProduct->manufacturer->name,
                'model' => $unsavedProduct->model,
                'type' => $unsavedProduct->type->slug,
                'workOrderId' => $workOrder->luhn,
            ];
            $product = ProductStoreAction::execute(ProductStoreObject::fromRequest($formRequest));
        }

        $this->actingAs($this->user)
            ->get(route(AjaxSearchController::INDEX_NAME, ['q' => $product->serial]))
            ->assertJsonFragment(
                [
                    'name' => $serial,
                    'url' => '/inventory/' . $product->luhn,
                ]
            );
        $this->actingAs($this->user)
            ->get(route(AjaxSearchController::INDEX_NAME, ['q' => substr($product->serial, 1, 2)]))
            ->assertJsonFragment(
                [
                    'name' => $serial,
                    'url' => '/inventory/' . $product->luhn,
                ]
            );
    }
}
