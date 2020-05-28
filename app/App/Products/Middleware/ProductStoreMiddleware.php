<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Middleware;

use Closure;
use Domain\Products\Models\Product;
use Illuminate\Http\Request;

/**
 * Class ProductStoreMiddleware
 *
 * @package App\Products\Middleware
 */
class ProductStoreMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Adds all request variables except for those listed to the 'values' key.
     * The 'values' key is used to add variable
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next(
            $request->merge(
                [
                    Product::VALUES => $request->except(
                        Product::MANUFACTURER_NAME,
                        Product::MODEL,
                        Product::TYPE,
                        Product::WORK_ORDER_ID
                    ),
                ]
            )
        );
    }
}
