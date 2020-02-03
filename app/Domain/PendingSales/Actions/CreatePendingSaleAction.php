<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;

/**
 * Class CreatePendingSaleAction
 *
 * @package Domain\PendingSales\Actions
 */
class CreatePendingSaleAction
{

    /**
     * @param Cart $cart
     * @param Product $product
     * @return Product
     */
    public static function execute(Cart $cart, Product $product): Product
    {
        $cart->products()->save($product);

        return $product;
    }
}
