<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use App\Carts\Requests\PendingSalesStoreRequest;
use Domain\Carts\Models\Cart;
use Domain\PendingSales\Actions\PendingSalesDestroyAction;
use Domain\PendingSales\Actions\PendingSalesStoreAction;
use Domain\Products\Models\Product;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
     * @param PendingSalesStoreRequest $request
     * @return JsonResponse
     */
    public function store(PendingSalesStoreRequest $request): JsonResponse
    {
        $product = Product::find($request->input(Product::ID));
        $cart = Cart::find($request->input(Product::CART_ID));
        if ($product->cart_id !== null) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json(
            PendingSalesStoreAction::execute(
                $cart,
                $product
            )
        )->setStatusCode(201);
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        return response()->json(PendingSalesDestroyAction::execute($product));
    }
}
