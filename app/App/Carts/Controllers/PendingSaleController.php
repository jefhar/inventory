<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Carts\Controllers;

use App\Admin\Controllers\Controller;
use App\Carts\Requests\PendingSalesStoreRequest;
use App\Carts\Resources\ProductResource;
use Domain\Carts\Models\Cart;
use Domain\PendingSales\Actions\PendingSalesDestroyAction;
use Domain\PendingSales\Actions\PendingSalesStoreAction;
use Domain\PendingSales\DataTransferObjects\PendingSalesStoreObject;
use Domain\Products\Models\Product;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PendingSalesController
 *
 * @package App\Carts\Controllers
 */
class PendingSaleController extends Controller
{

    public const STORE_NAME = 'pendingSales.store';
    public const STORE_PATH = '/pendingSales';
    public const DESTROY_NAME = 'pendingSales.destroy';
    public const DESTROY_PATH = '/pendingSales/{product}';

    /**
     * @param PendingSalesStoreRequest $request
     * @noinspection NullPointerExceptionInspection
     * @return JsonResponse
     */
    public function store(PendingSalesStoreRequest $request): JsonResponse
    {
        $pendingSalesStoreObject = PendingSalesStoreObject::fromRequest($request->validated());
        $product = Product::find($pendingSalesStoreObject->product_id);
        $cart = Cart::find($pendingSalesStoreObject->cart_id);

        if ($product->cart_id !== null) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return (new ProductResource(
            PendingSalesStoreAction::execute($cart, $product)
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Product $product
     */
    public function destroy(Product $product)
    {
        // return response()->json(PendingSalesDestroyAction::execute($product));
        return new ProductResource(PendingSalesDestroyAction::execute($product));
    }
}
