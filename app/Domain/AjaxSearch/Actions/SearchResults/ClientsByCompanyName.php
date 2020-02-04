<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

use Domain\WorkOrders\Models\Client;
use Illuminate\Support\Collection;

/**
 * Class ClientsByCompanyName
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
class ClientsByCompanyName extends AbstractSearchAction
{

    /**
     * @inheritDoc
     */
    public function search(string $searchString): Collection
    {
        $client_ids = Client::findByCompanyName($searchString);

        return Client::whereIn(Client::ID, $client_ids)
            ->get()
            ->map(
                fn($client) => [
                    'name' => $client->company_name,
                    'url' => '/clients/' . $client->id,
                ]
            );
    }
}
