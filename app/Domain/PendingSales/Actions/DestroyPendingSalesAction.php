<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

use Domain\Products\Models\Product;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DestroyPendingSalesAction
 *
 * @package Domain\PendingSales\Actions
 */
class DestroyPendingSalesAction
{

    /**
     * @param Product $product
     * @return Product
     */
    public static function execute(Product $product): Product
    {
        if ($product->status !== Product::STATUS_IN_CART) {
            abort(Response::HTTP_FORBIDDEN);
        }
        $product->cart()->dissociate();
        $product->status = Product::STATUS_AVAILABLE;
        $product->save();

        return $product;
    }
}
