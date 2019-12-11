<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions;

use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AjaxSearch
 *
 * @package Domain\AjaxSearch\Actions
 */
class AjaxSearchAction
{
    /**
     * @param string $field ENUM {Client::COMPANY_NAME|manufacturer}
     * @param string $searchString
     * @return Collection
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public static function findBy(string $field, string $searchString): Collection
    {
        $availableFields = [
            'manufacturer' => 'manufacturer',
            Client::COMPANY_NAME => Client::COMPANY_NAME,
            Product::MODEL => Product::MODEL,
        ];
        if (!array_key_exists($field, $availableFields)) {
            abort(Response::HTTP_NOT_ACCEPTABLE);
        }

        switch ($field) {
            case Client::COMPANY_NAME:
                return self::clientsAndPeopleByCompanyName($searchString);
                break;
            case 'manufacturer':
                return self::manufacturersByManufacturerName($searchString);
                break;
            case Product::MODEL:
                return self::productsByModel($searchString);
                break;
        }
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    private static function clientsAndPeopleByCompanyName(string $searchString): Collection
    {
        $client_ids = Client::findByCompanyName($searchString);
        $clients = Client::whereIn(Client::ID, $client_ids)->with('person')->get();

        return $clients->map(
            fn($item) => [
                Person::CLIENT_ID => $item->id,
                Client::COMPANY_NAME => $item->company_name,
                Person::FIRST_NAME => $item->person->first_name,
                Person::LAST_NAME => $item->person->last_name,
            ]
        );
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    private static function manufacturersByManufacturerName(string $searchString): Collection
    {
        $searchString = "%{$searchString}%";

        return Manufacturer::where(Manufacturer::NAME, 'like', $searchString)->get()->pluck(Manufacturer::NAME)->unique(
        )->values();
    }

    private static function productsByModel(string $searchString): Collection
    {
        $searchString = "%{$searchString}%";

        return Product::where(Product::MODEL, 'like', $searchString)->get()->pluck(Product::MODEL)->unique()->values();
    }

    public static function findAll(string $searchString): Collection
    {
        $client_ids = Client::findByCompanyName($searchString);
        $clients = Client::whereIn(Client::ID, $client_ids)->get()->map(
            fn($client) => [
                'name' => $client->company_name,
                'url' => '/clients/' . $client->id,
            ]
        );

        $people_ids = Person::findByName($searchString);
        $people = Person::whereIn(Person::ID, $people_ids)->get()->map(
            fn($person) => [
                'name' => $person->first_name . ' ' . $person->last_name,
                'url' => '/clients/' . $person->client_id,
            ]
        );

        $collection = collect();
        $collection = $collection->concat($clients);
        $collection = $collection->concat($people);

        return $collection;
    }
}
