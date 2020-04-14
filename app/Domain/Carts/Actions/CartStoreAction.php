<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use App\Carts\DataTransferObjects\CartStoreObject;
use Domain\Carts\Models\Cart;
use Domain\WorkOrders\Models\Client;
use Illuminate\Support\Facades\Auth;

/**
 * Class CartStoreAction
 *
 * @package Domain\Carts\Actions
 */
class CartStoreAction
{

    /**
     * @param CartStoreObject $cartStoreObject
     * @return Cart
     * @todo figure out what data the cart needs, put it in cartStoreObject
     */
    public static function execute(CartStoreObject $cartStoreObject): Cart
    {
        $user = Auth::user();
        $cart = new Cart();
        $client = Client::firstOrCreate([Client::COMPANY_NAME => $cartStoreObject->company_name]);
        $cart->client()->associate($client);
        $user->carts()->save($cart);

        return $cart;
    }
}
