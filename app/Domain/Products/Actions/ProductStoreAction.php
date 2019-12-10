<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace Domain\Products\Actions;

use App\Products\DataTransferObject\ProductStoreObject;
use Domain\Products\Models\Manufacturer;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\WorkOrder;

/**
 * Class ProductStoreAction
 *
 * @package App\Products\Controllers\Domain\Products\Actions
 */
class ProductStoreAction
{

    /**
     * @param $productStoreObject
     * @return Product
     */
    public static function execute(ProductStoreObject $productStoreObject): Product
    {
        $workOrder = WorkOrder::findOrFail($productStoreObject->workOrderId);

        $type = Type::where(Type::SLUG, $productStoreObject->type)->first();

        $manufacturer = Manufacturer::firstOrCreate([Manufacturer::NAME => $productStoreObject->manufacturer]);

        $product = new Product(
            [
                Product::MODEL => $productStoreObject->model,
                Product::VALUES => $productStoreObject->values,
            ]
        );

        $product->type()->associate($type);
        $product->manufacturer()->associate($manufacturer);
        $workOrder->products()->save($product);
        $product->save();

        return $product;
    }
}
