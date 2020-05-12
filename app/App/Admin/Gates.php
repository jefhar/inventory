<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Admin;

use App\User;
use Domain\Carts\Models\Cart;

class Gates
{

    public const INVOICE_CART = 'invoice cart';
    public const DESTROY_CART = 'destroy cart';

    /**
     * @param Cart $cart
     * @param User $user
     * @return bool
     */
    public static function cartBelongsToUser(Cart $cart, User $user): bool
    {
        return $cart->user_id === $user->id;
    }
}
