<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions;

use App\AjaxSearch\DataTransferObjects\AjaxSearchObject;
use App\AjaxSearch\Requests\AjaxSearchRequest;
use Domain\AjaxSearch\Actions\SearchResults\ClientsByCompanyName;
use Domain\AjaxSearch\Actions\SearchResults\ClientsWithPersonByCompanyName;
use Domain\AjaxSearch\Actions\SearchResults\ManufacturersByManufacturerName;
use Domain\AjaxSearch\Actions\SearchResults\PeopleByName;
use Domain\AjaxSearch\Actions\SearchResults\ProductsByModel;
use Domain\AjaxSearch\Actions\SearchResults\ProductsBySerial;
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
     * @param AjaxSearchObject $ajaxSearchObject
     * @return Collection
     */
    public static function findBy(AjaxSearchObject $ajaxSearchObject): Collection
    {
        $field = $ajaxSearchObject->field;
        $searchString = $ajaxSearchObject->q;
        $searchResults = null;
        switch ($field) {
            case AjaxSearchRequest::SEARCH_COMPANY_NAME:
                $searchResults = ClientsWithPersonByCompanyName::getInstance();
                break;
            case AjaxSearchRequest::SEARCH_MANUFACTURER:
                $searchResults = ManufacturersByManufacturerName::getInstance();
                break;
            case AjaxSearchRequest::SEARCH_MODEL:
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
