<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\PendingSales\Actions;

/**
 * Class DestroyPendingSalesAction
 *
 * @package Domain\PendingSales\Actions
 */
class DestroyPendingSalesAction
{

    public static function execute(\Domain\Products\Models\Product $product)
    {
        $product->cart()->dissociate();
        $product->save();

        return $product;
    }
}
