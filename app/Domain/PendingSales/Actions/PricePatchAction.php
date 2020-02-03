<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

use Domain\Products\Models\Product;

/**
 * Class PricePatchAction
 *
 * @package Domain\PendingSales\Actions
 */
class PricePatchAction
{

    /**
     * @param Product $product
     * @param int $price
     * @return Product
     */
    public static function execute(Product $product, int $price): Product
    {
        $product->price = $price;
        $product->save();

        return $product;
    }
}
