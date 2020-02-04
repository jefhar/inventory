<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

use Illuminate\Support\Collection;

/**
 * Class AbstractSearchAction
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
abstract class AbstractSearchAction implements SearchActionInterface
{

    /**
     * @return SearchActionInterface
     */
    public static function getInstance(): SearchActionInterface
    {
        return new static;
    }

    /**
     * @param string $searchString
     * @return Collection
     */
    abstract public function search(string $searchString): Collection;
}
