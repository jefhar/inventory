<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

use Domain\Products\Models\Product;

/**
 * Class DestroyPendingSalesAction
 *
 * @package Domain\PendingSales\Actions
 */
class DestroyPendingSalesAction
{

    /**
     * @param Product $product
     * @return Product
     */
    public static function execute(Product $product): Product
    {
        $product->cart()->dissociate();
        $product->save();

        return $product;
    }
}
