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
use Domain\PendingSales\Actions\PricePatchAction;
use Domain\Products\Actions\ProductStoreAction;
use Domain\Products\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProductsController
 *
 * @package App\Products\Controllers
 */
class ProductsController extends Controller
{
    public const STORE_NAME = 'products.store';
    public const STORE_PATH = '/products';
    public const UPDATE_NAME = 'products.update';
    public const UPDATE_PATH = '/products/{product}';

    /**
     * @param ProductStoreRequest $request
     * @return Product
     */
    public function store(ProductStoreRequest $request): Product
    {
        $productStoreObject = ProductStoreObject::fromRequest($request->validated());

        return ProductStoreAction::execute($productStoreObject);
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return Product
     */
    public function update(Product $product, Request $request): Product
    {
        $price = $request->input(Product::PRICE);
        if ($price < 0) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $price = (floor($price * 100)) / 100;

        return PricePatchAction::execute($product, $price);
    }
}
