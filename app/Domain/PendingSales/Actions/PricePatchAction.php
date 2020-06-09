<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

use App\Admin\Exceptions\LockedProductException;
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
     * @param float $price
     * @return Product
     */
    public static function execute(Product $product, float $price): Product
    {
        if ($product->status === Product::STATUS_INVOICED) {
            throw new LockedProductException("Product is already invoiced.");
        }
        $priceInPennies = floor($price * 100) / 100;
        $product->price = $priceInPennies;
        $product->save();

        return $product;
    }
}
