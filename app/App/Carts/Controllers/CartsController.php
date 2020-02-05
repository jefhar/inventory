<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use App\Admin\Permissions\UserPermissions;
use App\Carts\DataTransferObjects\CartStoreObject;
use App\Carts\Requests\CartStoreRequest;
use Domain\Carts\Actions\CartStoreAction;
use Domain\Carts\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function index()
    {
        // Same code in InventoryController::show
        if (Auth::user()->can(UserPermissions::SEE_ALL_OPEN_CARTS)) {
            $carts = Cart::where(Cart::STATUS, Cart::STATUS_OPEN)->get();
        } else {
            $carts = Auth::user()->carts()->get();
        }
        return view('carts.index')
            ->with(
                [
                    'name' => Auth::user()->name,
                    'carts' => $carts,
                ]
            );
    }

    /**
     * @param Cart $cart
     * @return View
     */
    public function show(Cart $cart): View
    {
        return view('carts.show')->with(['cart' => $cart]);
    }

    /**
     * @param CartStoreRequest $request
     * @return Cart
     */
    public function store(CartStoreRequest $request): Cart
    {
        $cartStoreObject = CartStoreObject::fromRequest($request->validated());

        return CartStoreAction::execute($cartStoreObject);
    }

    /**
     * @return JsonResponse
     */
    public function update(): JsonResponse
    {
        return response()->json(['id' => 1, 'status' => 'void']);
    }
}
