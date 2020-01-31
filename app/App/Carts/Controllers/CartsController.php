<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use Domain\Carts\Models\Cart;

/**
 * Class CartsController
 *
 * @package App\Carts\Controllers\
 */
class CartsController extends Controller
{
    public const DESTROY_NAME = 'carts.destroy';
    public const DESTROY_PATH = '/carts/{cart}';
    public const INDEX_NAME = 'carts.index';
    public const INDEX_PATH = '/carts';
    public const SHOW_NAME = 'carts.show';
    public const SHOW_PATH = '/carts/{cart}';
    public const STORE_NAME = 'carts.store';
    public const STORE_PATH = '/carts';
    public const UPDATE_NAME = 'carts.update';
    public const UPDATE_PATH = '/carts/{cart}';

    public function destroy()
    {
    }

    public function index()
    {
    }

    public function show(Cart $cart)
    {
        return view('carts.show')->with(['cart' => $cart]);
    }

    public function store(\App\Carts\Requests\CartStoreRequest $request): \Domain\Carts\Models\Cart
    {
        $cartStoreObject = \App\Carts\DataTransferObjects\CartStoreObject::fromRequest($request->validated());

        return \Domain\Carts\Actions\CartStoreAction::execute($cartStoreObject);
    }

    public function update()
    {
        return response()->json(['id' => 1, 'status' => 'void']);
    }
}
