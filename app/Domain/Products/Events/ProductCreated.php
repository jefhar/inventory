<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Events;

use Domain\Products\Models\Product;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProductCreated
 *
 * @package Domain\Products\Events
 */
class ProductCreated
{
    use SerializesModels;
    public Product $product;

    /**
     * ProductCreated constructor.
     *
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}
