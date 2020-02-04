<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use Domain\Carts\Models\Cart;
use Domain\PendingSales\Actions\CreatePendingSaleAction;
use Domain\PendingSales\Actions\DestroyPendingSalesAction;
use Domain\Products\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PendingSalesController
 *
 * @package App\Carts\Controllers
 */
class PendingSalesController extends Controller
{

    public const STORE_NAME = 'pendingSales.store';
    public const STORE_PATH = '/pendingSales';
    public const DESTROY_NAME = 'pendingSales.destroy';
    public const DESTROY_PATH = '/pendingSales/{product}';

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json(
            CreatePendingSaleAction::execute(
                Cart::findOrFail($request->input(Product::CART_ID)),
                Product::findOrFail($request->input(Product::ID))
            )
        )->setStatusCode(201);
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        return response()->json(DestroyPendingSalesAction::execute($product));
    }
}
