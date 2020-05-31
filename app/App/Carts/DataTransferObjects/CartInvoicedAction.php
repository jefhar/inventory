<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\DataTransferObjects;

use App\Admin\Gates;
use Domain\Carts\CartInvoiced;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class CartInvoicedAction
{

    /**
     * @param Cart $cart
     * @return Cart
     */
    public static function execute(Cart $cart): Cart
    {
        Gate::authorize(Gates::INVOICE_CART, $cart);
        DB::table(Product::TABLE)
            ->where(Product::CART_ID, $cart->id)
            ->update(
                [
                    Product::STATUS => Product::STATUS_INVOICED,
                ]
            );

        $cart->status = Cart::STATUS_INVOICED;
        $cart->save();

        Mail::to($cart->client->person)
            ->cc(Auth::user())
            ->queue(new CartInvoiced($cart));

        return $cart;
    }
}
