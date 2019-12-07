<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Controllers;

use App\Admin\Controllers\Controller;
use Domain\Products\Models\Product;
use Domain\Products\Models\Type;
use Domain\WorkOrders\WorkOrder;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public const STORE_NAME = 'products.store';
    public const STORE_PATH = '/products';

    /**
     * @param Request $request
     * @return Product
     */
    public function store(Request $request): Product
    {
        $workOrder = WorkOrder::findOrFail($request->get('workorderId'));
        $type = Type::where(Type::SLUG, $request->get('type'))->first();
        $values = json_encode($request->except(['workorderId', 'type']));
        $product = new Product();
        $product->values = $values;
        $product->type()->associate($type);
        $workOrder->products()->save($product);
        $product->save();

        return $product;
    }
}
