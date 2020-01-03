<?php

/**
 * Copyright 2018, 2019, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Controllers;

use App\Admin\Controllers\Controller;
use App\Products\DataTransferObject\ProductUpdateObject;
use App\Products\Requests\ProductUpdateRequest;
use Domain\Products\Actions\ProductUpdateAction;
use Domain\Products\Models\Product;
use Illuminate\View\View;

/**
 * Class InventoryController
 *
 * @package App\Products\Controllers
 */
class InventoryController extends Controller
{
    public const INDEX_NAME = 'inventory.index';
    public const INDEX_PATH = '/inventory';
    public const SHOW_NAME = 'inventory.show';
    public const SHOW_PATH = '/inventory/{product}';
    public const UPDATE_NAME = 'inventory.update';
    public const UPDATE_PATH = '/inventory/{product}';

    /**
     * @return View
     */
    public function index(): View
    {
        $products = Product::paginate(25);

        return view('inventory.index', ['products' => $products]);
    }

    /**
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        return view('inventory.show', ['product' => $product]);
    }

    /**
     * @param Product $product
     * @param ProductUpdateRequest $request
     * @return Product
     */
    public function update(Product $product, ProductUpdateRequest $request): Product
    {
        $productUpdateObject = ProductUpdateObject::fromRequest($request->validated());
        $product = ProductUpdateAction::execute($product, $productUpdateObject);

        return $product;
    }

}
