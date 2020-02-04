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
 * Class ProductsBySerial
 *
 * @package Domain\AjaxSearch\Actions\SearchResults
 */
class ProductsBySerial extends AbstractSearchAction
{
    use SearchActionTrait;

    /**
     * @param string $searchString
     * @return Collection
     */
    public function search(string $searchString): Collection
    {
        $product_ids = Product::findBySerial($searchString);

        return Product::whereIn(Product::ID, $product_ids)
            ->get()
            ->map(
                fn($product) => [
                'name' => $product->serial,
                'url' => '/inventory/' . $product->luhn,
                ]
            );
    }
}
