<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Middleware;

use App\Products\Requests\ProductStoreRequest;
use Closure;
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
                    ProductStoreRequest::VALUES => $request->except(
                        ProductStoreRequest::MANUFACTURER_NAME,
                        ProductStoreRequest::MODEL,
                        ProductStoreRequest::TYPE,
                        ProductStoreRequest::WORK_ORDER_ID
                    ),
                ]
            )
        );
    }
}
