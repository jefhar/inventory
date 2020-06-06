<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use App\Admin\Permissions\UserPermissions;
use App\Carts\DataTransferObjects\CartPatchObject;
use App\Carts\DataTransferObjects\CartStoreObject;
use App\Carts\Requests\CartPatchRequest;
use App\Carts\Requests\CartStoreRequest;
use App\Carts\Resources\CartResource;
use App\User;
use Domain\Carts\Actions\CartDestroyAction;
use Domain\Carts\Actions\CartPatchAction;
use Domain\Carts\Actions\CartStoreAction;
use Domain\Carts\Models\Cart;
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

    public function destroy(Cart $cart)
    {
        CartDestroyAction::execute($cart);

        return $cart;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        // Same code in InventoryController::show
        if ($user->can(UserPermissions::SEE_ALL_OPEN_CARTS)) {
            $carts = Cart::where(Cart::STATUS, Cart::STATUS_OPEN)->orderBy(Cart::CREATED_AT)->get();
        } else {
            $carts = $user->carts()->get();
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
     */
    public function store(CartStoreRequest $request): CartResource
    {
        // New Cart is sent with a productId;
        $cartStoreObject = CartStoreObject::fromRequest($request->validated());

        return new CartResource(CartStoreAction::execute($cartStoreObject));
    }

    /**
     * @param Cart $cart
     * @param CartPatchRequest $request
     * @return CartResource
     * @throws \Exception
     */
    public function update(Cart $cart, CartPatchRequest $request): CartResource
    {
        $cartPatchObject = CartPatchObject::fromRequest($request->validated());

        return new CartResource(CartPatchAction::execute($cart, $cartPatchObject));
    }
}
