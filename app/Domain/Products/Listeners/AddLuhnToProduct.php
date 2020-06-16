<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Listeners;

use Domain\Products\Events\ProductCreated;
use Tdely\Luhn\Luhn;

/**
 * Class AddLuhnToProduct
 *
 * @package Domain\Products\Listeners
 */
class AddLuhnToProduct
{
    /**
     * @param ProductCreated $event
     */
    public function handle(ProductCreated $event): void
    {
        $id = $event->product->id;
        $luhn = Luhn::create($id);
        $event->product->luhn = $luhn;
        $event->product->save();
    }
}
