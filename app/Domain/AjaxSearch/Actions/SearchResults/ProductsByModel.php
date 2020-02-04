<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\AjaxSearch\Actions\SearchResults;

use Domain\Products\Models\Product;
use Illuminate\Support\Collection;

/**
 * Class ProductsByModel
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
class ProductsByModel extends AbstractSearchAction
{
    use SearchActionTrait;

    /**
     * @param string $searchString
     * @return Collection
     */
    public function search(string $searchString): Collection
    {
        $searchString = "%{$searchString}%";

        return Product::where(Product::MODEL, 'like', $searchString)
            ->get()
            ->pluck(Product::MODEL)
            ->unique()
            ->values();
    }
}
