<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Listeners;

use Domain\Carts\Events\CartCreated;
use Tdely\Luhn\Luhn;

/**
 * Class AddLuhnToCart
 *
 * @package Domain\Carts\Listeners
 */
class AddLuhnToCart
{
    /**
     * @param CartCreated $event
     */
    public function handle(CartCreated $event): void
    {
        $event->cart->luhn = Luhn::create($event->cart->id);
        $event->cart->save();
    }
}
