<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Controllers;

use App\Admin\Controllers\Controller;
use App\Products\DataTransferObject\ProductStoreObject;
use App\Products\Requests\ProductStoreRequest;
use Domain\Products\Actions\ProductStoreAction;
use Domain\Products\Models\Product;

/**
 * Class ProductsController
 *
 * @package App\Products\Controllers
 */
class ProductsController extends Controller
{
    public const STORE_NAME = 'products.store';
    public const STORE_PATH = '/products';

    /**
     * @param ProductStoreRequest $request
     * @return Product
     */
    public function store(ProductStoreRequest $request): Product
    {
        $productStoreObject = ProductStoreObject::fromRequest($request->validated());

        return ProductStoreAction::execute($productStoreObject);
    }
}
