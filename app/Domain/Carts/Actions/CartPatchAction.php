<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use App\Carts\DataTransferObjects\CartPatchObject;
use Domain\Carts\Models\Cart;

/**
 * Class CartPatchAction
 *
 * @package Domain\Carts\Actions
 */
class CartPatchAction
{

    /**
     * @param Cart $cart
     * @param CartPatchObject $cartPatchObject
     * @return Cart
     */
    public static function execute(Cart $cart, CartPatchObject $cartPatchObject): Cart
    {
        $cart->status = $cartPatchObject->status;
        $cart->save();

        return $cart;
    }
}
