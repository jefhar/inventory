<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions;

use Domain\WorkOrders\Client;
use Domain\WorkOrders\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

use function strtolower;

/**
 * Class AjaxSearch
 *
 * @package Domain\AjaxSearch\Actions
 */
class AjaxSearchAction
{

    /**
     * @param string $field ENUM {Client::COMPANY_NAME|}
     * @param string $searchString
     * @return Collection
     */
    public static function findBy(string $field, string $searchString): Collection
    {
        $availableFields = [
            Client::COMPANY_NAME => Client::COMPANY_NAME,
        ];
        if (!array_key_exists($field, $availableFields)) {
            abort(Response::HTTP_NOT_ACCEPTABLE);
        }

        //switch ($field) {
        //    case Client::COMPANY_NAME:
        return self::clientsAndPeopleByCompanyName($searchString);
        //        break;
        //}
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    private static function clientsAndPeopleByCompanyName(string $searchString): Collection
    {
        $client_ids = Client::findByCompanyName($searchString);
        $clients = Client::whereIn(Client::ID, $client_ids)->with('person')->get();

        $map = $clients->map(
            static function ($item) {
                return [
                    Person::CLIENT_ID => $item->id,
                    Client::COMPANY_NAME => $item->company_name,
                    Person::FIRST_NAME => $item->person->first_name,
                    Person::LAST_NAME => $item->person->last_name,
                ];
            }
        );

        return $map;
    }

    public static function findAll(string $searchString): Collection
    {
        $client_ids = Client::findByCompanyName($searchString);
        $clients = Client::whereIn(Client::ID, $client_ids)->get();

        $people_ids = Person::findByName($searchString);
        $people = Person::whereIn(Person::ID, $people_ids)->get();

        $peopleMap = $people->map(
            static function ($person) use ($searchString) {
                $name = $person->first_name;
                if (Str::contains(strtolower($person->last_name), strtolower($searchString))) {
                    $name = $person->last_name;
                }

                return [
                    'name' => $name,
                    'search' => $searchString,
                    'type' => 'person',
                    'url' => '/clients/' . $person->client_id,
                ];
            }
        );

        $clientsMap = $clients->map(
            static function ($client) use ($searchString) {
                return [
                    'name' => $client->company_name,
                    'search' => $searchString,
                    'type' => 'client',
                    'url' => '/clients/' . $client->id,
                ];
            }
        );
        $collection = collect();
        $collection = $collection->concat($clientsMap);
        $collection = $collection->concat($peopleMap);

        return $collection;
    }
}
