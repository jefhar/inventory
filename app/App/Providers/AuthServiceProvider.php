<?php

namespace App\Providers;

use App\Admin\Gates;
use App\User;
use Domain\Carts\Models\Cart;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define(
            Gates::DESTROY_CART,
            function (User $user, Cart $cart) {
                return Gates::cartBelongsToUser($cart, $user);
            }
        );
        Gate::define(
            Gates::INVOICE_CART,
            function (User $user, Cart $cart) {
                return Gates::cartBelongsToUser($cart, $user);
            }
        );
    }
}
