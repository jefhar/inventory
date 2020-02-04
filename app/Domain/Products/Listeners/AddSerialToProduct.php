<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Listeners;

use Domain\Products\Events\ProductSaved;

/**
 * Class AddSerialToProduct
 *
 * @package Domain\Products\Listeners
 */
class AddSerialToProduct
{
    /**
     * @param ProductSaved $event
     */
    public function handle(ProductSaved $event): void
    {
        $product = $event->product;
        $potentialSerial = ($product->values['serial']) ?? null;
        if ($potentialSerial && ($potentialSerial !== $product->serial)) {
            $product->serial = $potentialSerial;
            $product->save();
        }
    }
}
