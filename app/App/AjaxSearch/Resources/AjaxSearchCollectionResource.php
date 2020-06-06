<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\AjaxSearch\Resources;

use Domain\WorkOrders\Models\Client;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * @property Client $client
 */
class AjaxSearchCollectionResource extends ResourceCollection
{

    public const CLIENT_COMPANY_NAME = 'client_company_name';
    public const CLIENT_FIRST_NAME = 'client_first_name';
    public const CLIENT_ID = 'client_id';
    public const CLIENT_LAST_NAME = 'client_last_name';

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $element = $this->collection->first();

        if (is_array($element)) {
            if (array_key_exists('client_id', $element)) {
                return $this->convertToClients($this->collection);
            }
        }

        return $this->collection->toArray();
    }

    /**
     * @param Collection $collection
     * @return array
     */
    private function convertToClients($collection): array
    {
        $response = [];
        foreach ($collection as $element) {
            $response[] = [
                self::CLIENT_ID => $element['client_id'],
                self::CLIENT_COMPANY_NAME => $element['company_name'],
                self::CLIENT_FIRST_NAME => $element['first_name'],
                self::CLIENT_LAST_NAME => $element['last_name'],
            ];
        }

        return $response;
    }
}
