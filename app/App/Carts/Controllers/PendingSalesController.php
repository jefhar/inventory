<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use Domain\Carts\Models\Cart;
use Domain\Products\Models\Product;
use Illuminate\Http\Request;

class PendingSalesController extends Controller
{

    public const STORE_NAME = 'pendingSales.store';
    public const STORE_PATH = '/pendingSales';
    public const DESTROY_NAME = 'pendingSales.destroy';
    public const DESTROY_PATH = '/pendingSales/';

    public function store(Request $request)
    {
        $cart = Cart::findOrFail($request->input(Product::CART_ID));
        $product = Product::findOrFail($request->input(Product::ID));

        return response()->json(
            \Domain\PendingSales\Actions\CreatePendingSaleAction::execute($cart, $product)
        )->setStatusCode(201);
    }

    public function destroy(Request $request)
    {
        $cart = Cart::findOrFail($request->input(Product::CART_ID));
        $product = Product::findOrFail($request->input(Product::ID));
       return response()->json(\Domain\PendingSales\Actions\DestroyPendingSalesAction::execute($product));
    }
}
