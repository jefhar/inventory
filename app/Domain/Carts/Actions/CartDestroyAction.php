<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\DB;

/**
 * Class CartDestroyAction
 *
 * @package Domain\Carts\Action
 */
class CartDestroyAction
{

    /**
     * @param Cart $cart
     * @return Cart
     * @throws \Exception Only if $cart doesn't have a primary key.
     */
    public static function execute(Cart $cart): Cart
    {
        DB::table(Product::TABLE)
            ->where(Product::CART_ID, $cart->id)
            ->update([Product::CART_ID => null]);
        $cart->delete();

        return $cart;
    }
}
