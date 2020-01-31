<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

/**
 * Class PricePatchAction
 *
 * @package Domain\PendingSales\Actions
 */
class PricePatchAction
{

    public static function execute(\Domain\Products\Models\Product $product, int $price)
    {
        $product->price = $price;
        $product->save();

        return $product;
    }
}
