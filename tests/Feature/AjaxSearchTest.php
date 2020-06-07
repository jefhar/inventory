<?php

/**
 * Copyright (c) 2018, 2019, 2020 Jeff Harris, C11K.
 */

declare(strict_types=1);

namespace Tests\Feature;

use App\AjaxSearch\Controllers\AjaxSearchController;
use App\AjaxSearch\Requests\AjaxSearchRequest;
use App\AjaxSearch\Resources\AjaxSearchCollectionResource;
use App\Products\DataTransferObject\ProductStoreObject;
use Domain\Products\Actions\ProductStoreAction;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Domain\WorkOrders\Models\WorkOrder;
use Faker\Factory;
use Support\Requests\ProductStore;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\FullObjects;

/**
 * Class AjaxSearchTest
 *
 * @package Tests\Feature
 */
class AjaxSearchTest extends TestCase
{
    use FullObjects;

    /**
     * @test
     */
    public function anonymousOrUnauthorizedIsUnauthorized(): void
    {
        $this->get(
            route(
                AjaxSearchController::SHOW_NAME,
                [AjaxSearchRequest::FIELD => AjaxSearchRequest::SEARCH_COMPANY_NAME]
            )
        )
            ->assertRedirect('/login');
    }

    /**
     * @test
     */
    public function authorizedIsOk(): void
    {
        $this->actingAs($this->createEmployee())
            ->get(
                route(
                    AjaxSearchController::SHOW_NAME,
                    [
                        AjaxSearchRequest::FIELD => AjaxSearchRequest::SEARCH_COMPANY_NAME,
                        AjaxSearchRequest::Q => 'a',
                    ]
                )
            )
            ->assertOk();
    }

    /**
     * @test
     */
    public function knownFieldIsOk(): void
    {
        $this->actingAs($this->createEmployee())
            ->get(
                route(
                    AjaxSearchController::SHOW_NAME,
                    [
                        AjaxSearchRequest::FIELD => AjaxSearchRequest::SEARCH_COMPANY_NAME,
                        AjaxSearchRequest::Q => 'foo',
                    ]
                )
            )
            ->assertOk();
    }

    /**
     * @test
     */
    public function unknownFieldIsNotAcceptable(): void
    {
        $this->actingAs($this->createEmployee())
            ->get(route(AjaxSearchController::SHOW_NAME, [
                AjaxSearchRequest::FIELD => 'flarp',
                AjaxSearchRequest::Q => 'q'
            ]))
            ->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @test
     */
    public function clientCompanyNameSearchReturnsJson(): void
    {
        $company_name = 'John';
        for ($i = 0; $i < 50; $i++) {
            /** @var Client $client */
            $client = factory(Client::class)->create();
            $person = factory(Person::class)->make();
            $client->person()->save($person);
        }
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $client->company_name = $company_name . uniqid('4', false);
        $client->save();
        $person = factory(Person::class)->make();
        $client->person()->save($person);

        /** @var Client $redHerringClient */
        $redHerringClient = factory(Client::class)->create();
        $redHerringClient->company_name = $company_name . uniqid('e', false);
        $redHerringClient->save();
        $red_herring_person = factory(Person::class)->make();
        $redHerringClient->person()->save($red_herring_person);

        $this->actingAs($this->createEmployee())
            ->get(
                route(
                    AjaxSearchController::SHOW_NAME,
                    [
                        AjaxSearchRequest::FIELD => AjaxSearchRequest::SEARCH_COMPANY_NAME,
                        AjaxSearchRequest::Q => 'J',
                    ]
                )
            )->assertJsonFragment(
                [
                    AjaxSearchCollectionResource::CLIENT_COMPANY_NAME => $client->company_name,
                    AjaxSearchCollectionResource::CLIENT_FIRST_NAME => $client->person->first_name,
                    AjaxSearchCollectionResource::CLIENT_LAST_NAME => $client->person->last_name,
                ]
            );
    }

    /**
     * @test
     */
    public function ajaxIndexReturnsDirectMatch(): void
    {
        /** @var Client $client */
        $client = factory(Client::class)->create();
        $this->actingAs($this->createEmployee())
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
        /** @var WorkOrder $workOrder */
        $workOrder = factory(WorkOrder::class)->create();
        $product = '';
        $serial = '';   // Why? so phpstan doesn't complain.
        for ($i = 0; $i < 15; $i++) {
            $serial = $faker->isbn13;
            /** @var Product $unsavedProduct */
            $unsavedProduct = factory(Product::class)->make();
            $formRequest = [
                ProductStore::VALUES => [
                    'radio-group-1575689472139' => $faker->word,
                    'select-1575689474390' => $faker->word,
                    'serial' => $serial,
                ],
                ProductStore::MANUFACTURER_NAME => $unsavedProduct->manufacturer->name,
                ProductStore::MODEL => $unsavedProduct->model,
                ProductStore::TYPE => $unsavedProduct->type->slug,
                ProductStore::WORK_ORDER_ID => $workOrder->luhn,
            ];
            $product = ProductStoreAction::execute(ProductStoreObject::fromRequest($formRequest));
        }

        $this->actingAs($this->createEmployee())
            ->get(route(AjaxSearchController::INDEX_NAME, ['q' => $product->serial]))
            ->assertJsonFragment(
                [
                    'name' => $serial,
                    'url' => '/inventory/' . $product->luhn,
                ]
            );
        $this->actingAs($this->createEmployee())
            ->get(route(AjaxSearchController::INDEX_NAME, ['q' => substr($product->serial, 1, 2)]))
            ->assertJsonFragment(
                [
                    'name' => $serial,
                    'url' => '/inventory/' . $product->luhn,
                ]
            );
    }
}
