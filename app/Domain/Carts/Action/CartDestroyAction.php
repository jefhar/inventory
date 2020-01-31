<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Action;

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
     * @param \Domain\Carts\Models\Cart $cart
     * @return \Domain\Carts\Models\Cart
     */
    public static function execute(\Domain\Carts\Models\Cart $cart): \Domain\Carts\Models\Cart
    {
        DB::table(Product::TABLE)
            ->where(Product::CART_ID, $cart->id)
            ->update([Product::CART_ID => null]);

        try {
            $cart->delete();
        } catch (\Exception $e) {
        }

        return $cart;
    }
}
