<?php
/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Products\Middleware;

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
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $values = $request->except('manufacturer', 'model', 'type', 'workorderId');
        $request = $request->merge(['values' => $values]);

        return $next($request);
    }
}
