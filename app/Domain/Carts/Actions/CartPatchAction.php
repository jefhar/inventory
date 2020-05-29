<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use App\Admin\Exceptions\InvalidAction;
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
     * @throws InvalidAction|\Exception
     */
    public static function execute(Cart $cart, CartPatchObject $cartPatchObject): Cart
    {
        switch ($cartPatchObject->status) {
            case Cart::STATUS_VOID:
                return CartDestroyAction::execute($cart);

            case Cart::STATUS_INVOICED:
                return CartInvoicedAction::execute($cart);
        }

        throw new InvalidAction($cartPatchObject->status . ' is not a valid status');
    }
}
