<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use App\Admin\Gates;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize(Gates::DESTROY_CART, $cart);
        DB::table(Product::TABLE)
            ->where(Product::CART_ID, $cart->id)
            ->update(
                [
                    Product::CART_ID => null,
                    Product::STATUS => Product::STATUS_AVAILABLE,
                ]
            );
        $cart->delete();
        $cart->status = Cart::STATUS_VOID;
        $cart->save();

        return $cart;
    }
}
