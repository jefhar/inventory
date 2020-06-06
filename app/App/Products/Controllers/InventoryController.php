<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Controllers;

use App\Admin\Controllers\Controller;
use App\Admin\Permissions\UserPermissions;
use App\Products\DataTransferObject\InventoryProductUpdateObject;
use App\Products\Requests\InventoryProductUpdateRequest;
use App\User;
use Domain\Carts\Models\Cart;
use Domain\Products\Actions\InventoryProductUpdateAction;
use Domain\Products\Actions\ProductShowAction;
use Domain\Products\Actions\InventoryProductUpdateAction;
use Domain\Products\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class InventoryController
 *
 * @package App\Products\Controllers
 * @TODO: combine this with ProductsController. Update will need to be joined. Loop over known array, check if there & update.
 */
class InventoryController extends Controller
{
    public const INDEX_NAME = 'inventory.index';
    public const INDEX_PATH = '/inventory';
    public const PAGINATE_PER_PAGE = 25;
    public const SHOW_NAME = 'inventory.show';
    public const SHOW_PATH = '/inventory/{product}';
    public const UPDATE_NAME = 'inventory.update';
    public const UPDATE_PATH = '/inventory/{product}';

    /**
     * @return View
     */
    public function index(): View
    {
        $products = Product::paginate(self::PAGINATE_PER_PAGE);

        return view('inventory.index', ['products' => $products]);
    }

    /**
     * @param Product $product
     * @return View
     * @throws \JsonException
     */
    public function show(Product $product): View
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user->can(UserPermissions::SEE_ALL_OPEN_CARTS)) {
            $carts = Cart::where(Cart::STATUS, Cart::STATUS_OPEN)->get();
        } else {
            $carts = $user->carts()->where(Cart::STATUS, Cart::STATUS_OPEN)->get();
        }
        $product->load('cart');
        $carts->load('client');

        return view(
            'inventory.show',
            [
                'product' => $product,
                'formData' => ProductShowAction::execute($product),
                'carts' => $carts,
            ]
        );
    }

    /**
     * @param Product $product
     * @param InventoryProductUpdateRequest $request
     * @return Product
     */
    public function update(Product $product, InventoryProductUpdateRequest $request): Product
    {
        $inventoryProductUpdateObject = InventoryProductUpdateObject::fromRequest($request->validated());

        return InventoryProductUpdateAction::execute($product, $inventoryProductUpdateObject);
    }
}
