<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

/**
 * Class CreatePendingSaleAction
 *
 * @package Domain\PendingSales\Actions
 */
class CreatePendingSaleAction
{

    public static function execute(\Domain\Carts\Models\Cart $cart, \Domain\Products\Models\Product $product)
    {
        $cart->products()->save($product);

        return $product;
    }
}
