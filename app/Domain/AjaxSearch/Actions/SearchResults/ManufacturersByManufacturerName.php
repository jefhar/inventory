<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

use Domain\Products\Models\Manufacturer;
use Illuminate\Support\Collection;

/**
 * Class ManufacturersByManufacturerName
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
class ManufacturersByManufacturerName extends AbstractSearchAction
{

    /**
     * @param string $searchString
     * @return Collection
     */
    public function search(string $searchString): Collection
    {
        $searchString = "%{$searchString}%";

        return Manufacturer::where(Manufacturer::NAME, 'like', $searchString)
            ->get()
            ->pluck(Manufacturer::NAME)
            ->unique()
            ->values();
    }
}
