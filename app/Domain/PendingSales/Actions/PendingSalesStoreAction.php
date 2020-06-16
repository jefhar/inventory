<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PendingSalesStoreAction
 *
 * @package Domain\PendingSales\Actions
 */
class PendingSalesStoreAction
{

    /**
     * @param Cart $cart
     * @param Product $product
     * @return Product
     */
    public static function execute(Cart $cart, Product $product): Product
    {
        if ($product->status !== Product::STATUS_AVAILABLE) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $product->status = Product::STATUS_IN_CART;
        $cart->products()->save($product);
        $product->load('cart');
        return $product;
    }
}
