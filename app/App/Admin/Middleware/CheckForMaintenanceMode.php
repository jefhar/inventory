<?php

namespace App\Admin\Middleware;

use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;

/**
 * Class CheckForMaintenanceMode
 *
 * @package App\Admin\Middleware
 * @codeCoverageIgnore
 */
class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
    ];
}
