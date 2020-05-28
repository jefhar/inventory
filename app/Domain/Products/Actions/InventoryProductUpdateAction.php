<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use App\Products\DataTransferObject\InventoryProductUpdateObject;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;

/**
 * Class ProductUpdateAction
 *
 * @package Domain\Products\Actions
 */
class InventoryProductUpdateAction
{
    /**
     * @param Product $product
     * @param InventoryProductUpdateObject $inventoryProductUpdateObject
     * @return Product
     */
    public static function execute(
        Product $product,
        InventoryProductUpdateObject $inventoryProductUpdateObject
    ): Product {
        $type = Type::where(Type::SLUG, $inventoryProductUpdateObject->type)->first();
        $manufacturer = Manufacturer::firstOrCreate(
            [Manufacturer::NAME => $inventoryProductUpdateObject->manufacturer_name]
        );
        $product = self::updateProduct($product, $inventoryProductUpdateObject);

        $product->type()->associate($type);
        $product->manufacturer()->associate($manufacturer);
        $product->save();

        return $product;
    }

    /**
     * @param Product $product
     * @param InventoryProductUpdateObject $inventoryProductUpdateObject
     * @return Product
     */
    private static function updateProduct(
        Product $product,
        InventoryProductUpdateObject $inventoryProductUpdateObject
    ): Product {
        $product->model = $inventoryProductUpdateObject->model;
        $product->values = $inventoryProductUpdateObject->values;

        return $product;
    }
}
