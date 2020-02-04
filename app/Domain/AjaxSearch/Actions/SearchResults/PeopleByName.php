<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

use Domain\WorkOrders\Models\Person;
use Illuminate\Support\Collection;

/**
 * Class PeopleByName
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
class PeopleByName extends AbstractSearchAction
{
    /**
     * @param string $searchString
     * @return Collection
     */
    public function search(string $searchString): Collection
    {
        $people_ids = Person::findByName($searchString);

        return Person::whereIn(Person::ID, $people_ids)
            ->get()
            ->map(
                fn($person) => [
                    'name' => $person->first_name . ' ' . $person->last_name,
                    'url' => '/clients/' . $person->client_id,
                ]
            );
    }
}
