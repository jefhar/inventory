<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions;

use Domain\AjaxSearch\Actions\SearchResults\ClientsByCompanyName;
use Domain\AjaxSearch\Actions\SearchResults\ClientsWithPersonByCompanyName;
use Domain\AjaxSearch\Actions\SearchResults\ManufacturersByManufacturerName;
use Domain\AjaxSearch\Actions\SearchResults\PeopleByName;
use Domain\AjaxSearch\Actions\SearchResults\ProductsByModel;
use Domain\AjaxSearch\Actions\SearchResults\ProductsBySerial;
use Domain\AjaxSearch\Actions\SearchResults\SearchActionInterface;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AjaxSearch
 *
 * @package Domain\AjaxSearch\Actions
 */
class AjaxSearchAction
{
    private const MANUFACTURER = 'manufacturer';

    /**
     * @param string $field ENUM {Client::COMPANY_NAME|manufacturer}
     * @param string $searchString
     * @return Collection
     */
    public static function findBy(string $field, string $searchString): Collection
    {
        /** @var SearchActionInterface $searchResults */
        switch ($field) {
            case Client::COMPANY_NAME:
                $searchResults = ClientsWithPersonByCompanyName::getInstance();
                break;
            case self::MANUFACTURER:
                $searchResults = ManufacturersByManufacturerName::getInstance();
                break;
            case Product::MODEL:
                $searchResults = ProductsByModel::getInstance();
                break;
            default:
                abort(Response::HTTP_NOT_ACCEPTABLE);
        }

        return $searchResults->search($searchString);
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    public static function findAll(string $searchString): Collection
    {
        $collection = collect();
        $collection = $collection->concat(ClientsByCompanyName::getInstance()->search($searchString));
        $collection = $collection->concat(PeopleByName::getInstance()->search($searchString));
        $collection = $collection->concat(ProductsBySerial::getInstance()->search($searchString));

        return $collection;
    }
}
