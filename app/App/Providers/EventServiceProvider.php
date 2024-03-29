<?php

/**
 * Copyright 2018, 2019, 2020 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Providers;

use Domain\Carts\Events\CartCreated;
use Domain\Carts\Listeners\AddLuhnToCart;
use Domain\Products\Events\ProductCreated;
use Domain\Products\Events\ProductSaved;
use Domain\Products\Listeners\AddLuhnToProduct;
use Domain\Products\Listeners\AddSerialToProduct;
use Domain\WorkOrders\Events\WorkOrderCreated;
use Domain\WorkOrders\Listeners\AddLuhnToWorkOrder;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        WorkOrderCreated::class => [
            AddLuhnToWorkOrder::class,
        ],
        ProductCreated::class => [
            AddLuhnToProduct::class,
        ],
        ProductSaved::class => [
            AddSerialToProduct::class,
        ],
        CartCreated::class => [
            AddLuhnToCart::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}
