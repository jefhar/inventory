<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use App\Products\DataTransferObject\ProductUpdateObject;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;

/**
 * Class ProductUpdateAction
 *
 * @package Domain\Products\Actions
 */
class ProductUpdateAction
{
    /**
     * @param Product $product
     * @param ProductUpdateObject $productUpdateObject
     * @return Product
     */
    public static function execute(Product $product, ProductUpdateObject $productUpdateObject): Product
    {
        $type = Type::where(Type::SLUG, $productUpdateObject->type)->first();

        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $productUpdateObject->manufacturer]);

        $product->model = $productUpdateObject->model;
        $product->values = $productUpdateObject->values;
        $product->type()->associate($type);
        $product->manufacturer()->associate($manufacturer);
        $product->save();

        return $product;
    }
}
