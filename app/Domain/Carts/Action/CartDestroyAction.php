<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Action;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     */
    public static function execute(Cart $cart): Cart
    {
        DB::table(Product::TABLE)
            ->where(Product::CART_ID, $cart->id)
            ->update([Product::CART_ID => null]);

        try {
            $cart->delete();
        } catch (\Exception $e) {
            Log::error('Attepting to delete cart id ' . $cart->id .".\n" . print_r($e, true));
        }

        return $cart;
    }
}
