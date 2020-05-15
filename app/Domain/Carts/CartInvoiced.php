<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts;

use Domain\Carts\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CartInvoiced extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Cart
     */
    public Cart $cart;

    /**
     * Create a new message instance.
     *
     * @param Cart $cart
     */
    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->markdown('emails.carts.invoiced');
    }
}
