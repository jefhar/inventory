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
use Symfony\Component\HttpFoundation\Response;

class Search
{

    /**
     * @param string $field ENUM {Client::COMPANY_NAME|}
     * @param $searchString
     * @return mixed
     */
    public static function findBy(string $field, $searchString)
    {
        switch ($field) {
            case Client::COMPANY_NAME:
                return self::ClientsAndPeopleByCompanyName($searchString);

                break;
            default:
                return Response::HTTP_NOT_ACCEPTABLE;
        }
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    private static function findClientsByCompanyName(string $searchString): Collection
    {
        return Client::where(Client::COMPANY_NAME, 'like', '%' . $searchString . '%')
            ->get()
            ->pluck(Client::ID, Client::ID);
    }

    /**
     * @param string $searchString
     * @return mixed
     */
    private static function ClientsAndPeopleByCompanyName(string $searchString)
    {
        $client_ids = self::findClientsByCompanyName($searchString);
        $clients = Client::whereIn(Client::ID, $client_ids)->with('person')->get();

        return $clients->map(
            function ($item, $key) {
                return [
                    Client::COMPANY_NAME => $item->company_name,
                    Person::FIRST_NAME => $item->person->first_name,
                    Person::LAST_NAME => $item->person->last_name,
                ];
            }
        );
    }
}
