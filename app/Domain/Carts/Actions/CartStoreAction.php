<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use Domain\Carts\Models\Cart;
use Illuminate\Support\Facades\Auth;

/**
 * Class CartStoreAction
 *
 * @package Domain\Carts\Actions
 */
class CartStoreAction
{

    /**
     * @param \App\Carts\DataTransferObjects\CartStoreObject $cartStoreObject
     * @return Cart
     */
    public static function execute(\App\Carts\DataTransferObjects\CartStoreObject $cartStoreObject): Cart
    {
        $cart = new Cart();
        $user = Auth::user();
        $user->carts()->save($cart);

        return $cart;
    }
}
