<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Events;

use Domain\Carts\Models\Cart;
use Illuminate\Queue\SerializesModels;

/**
 * Class CartCreated
 *
 * @package Domain\Carts\Events
 */
class CartCreated
{
    use SerializesModels;
    public Cart $cart;

    /**
     * CartCreated constructor.
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }
}
