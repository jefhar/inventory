<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use App\Products\DataTransferObject\RawProductUpdateObject;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;

/**
 * Class ProductUpdateAction
 *
 * @package Domain\Products\Actions
 */
class RawProductUpdateAction
{
    /**
     * @param Product $product
     * @param RawProductUpdateObject $rawProductUpdateObject
     * @return Product
     */
    public static function execute(Product $product, RawProductUpdateObject $rawProductUpdateObject): Product
    {
        $type = Type::where(Type::SLUG, $rawProductUpdateObject->type)->first();

        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $rawProductUpdateObject->manufacturer]);

        $product->model = $rawProductUpdateObject->model;
        $product->values = $rawProductUpdateObject->values;
        $product->type()->associate($type);
        $product->manufacturer()->associate($manufacturer);
        $product->save();

        return $product;
    }
}
