<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
use Illuminate\Support\Collection;

/**
 * Class ClientsAndPeopleByCompanyName
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
class ClientsWithPersonByCompanyName extends AbstractSearchAction
{
    use SearchActionTrait;

    /**
     * @param string $searchString
     * @return Collection
     */
    public function search(string $searchString): Collection
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
}
