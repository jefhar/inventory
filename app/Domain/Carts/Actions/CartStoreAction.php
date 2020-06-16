<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Carts\Actions;

use App\Carts\DataTransferObjects\CartStoreObject;
use App\User;
use App\WorkOrders\DataTransferObjects\PersonObject;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Domain\WorkOrders\Models\Client;
use Domain\WorkOrders\Models\Person;
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
     * @param PersonObject $personObject
     * @return Cart
     * @todo figure out what data the cart needs, put it in cartStoreObject
     */
    public static function execute(CartStoreObject $cartStoreObject, PersonObject $personObject): Cart
    {
        /** @var User $user */
        $user = Auth::user();
        $cart = new Cart();
        $product = Product::findOrFail($cartStoreObject->product_id);
        $product->status = Product::STATUS_IN_CART;
        $product->save();
        $client = Client::firstOrCreate([Client::COMPANY_NAME => $cartStoreObject->company_name]);
        $person = Person::firstOrNew(
            [
                Person::FIRST_NAME => $personObject->first_name ?: Person::DEFAULT_FIRST_NAME,
                Person::LAST_NAME => $personObject->last_name ?: Person::DEFAULT_LAST_NAME,
                Person::EMAIL => $personObject->email ?: date('U') . Person::DEFAULT_EMAIL,
                Person::PHONE_NUMBER => $personObject->phone_number ?: Person::DEFAULT_PHONE_NUMBER,
            ]
        );
        $cart->client()->associate($client);
        $user->carts()->save($cart);
        $cart->products()->save($product);
        $client->person()->save($person);

        return $cart;
    }
}
