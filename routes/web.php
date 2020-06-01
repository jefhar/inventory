<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Admin\Permissions\UserPermissions;
use App\AjaxSearch\Controllers\AjaxSearchController;
use App\Products\Controllers\InventoryController;
use App\Products\Controllers\ProductController;
use App\Types\Controllers\TypeController;
use App\WorkOrders\Controllers\ClientController;
use Domain\Carts\CartInvoiced;
use Domain\Carts\Models\Cart;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

Route::get(
    'mailable',
    function () {
        if (Auth::user() && (!App::environment('production'))) {
            $cart = Cart::find(1);

            return new CartInvoiced($cart);
        } else {
            abort(Response::HTTP_NOT_FOUND);
        }
    }
);
Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

Route::namespace('Admin\\Controllers')->group(
    function () {
        Auth::routes(['register' => false]);
    }
);
Route::group(
    ['middleware' => ['permission:' . UserPermissions::IS_EMPLOYEE]],
    function () {
        Route::namespace('Admin\\Controllers')->group(
            function () {
                Route::get('/home', 'HomeController@index')->name('home');
                Route::resource('dashboard', 'DashboardController');
            }
        );

        Route::namespace('WorkOrders\\Controllers\\')->group(
            function () {
                Route::resource('workorders', 'WorkOrderController')->only(
                    [
                        'create',
                        'edit',
                        'show',
                        'store',
                        'update',
                        'index',
                    ]
                )->middleware('auth');
            }
        );

        Route::namespace('AjaxSearch\\Controllers\\')->group(
            function () {
                Route::get(AjaxSearchController::SHOW_PATH, 'AjaxSearchController@show')
                    ->name(AjaxSearchController::SHOW_NAME)->middleware('auth');
                Route::get(AjaxSearchController::INDEX_PATH, 'AjaxSearchController@index')
                    ->name(AjaxSearchController::INDEX_NAME)->middleware('auth');
            }
        );
        Route::namespace('WorkOrders\\Controllers\\')->group(
            function () {
                Route::get(ClientController::SHOW_PATH, 'ClientController@show')
                    ->name(ClientController::SHOW_NAME)->middleware('auth');
            }
        );
        Route::namespace('Types\\Controllers\\')->group(
            function () {
                Route::get(TypeController::CREATE_PATH, 'TypeController@create')
                    ->name(TypeController::CREATE_NAME)->middleware('auth');
                Route::delete(TypeController::DESTROY_PATH, 'TypeController@destroy')
                    ->name(TypeController::DESTROY_NAME)->middleware('auth');
                Route::get(TypeController::INDEX_PATH, 'TypeController@index')
                    ->name(TypeController::INDEX_NAME)->middleware('auth');
                Route::get(TypeController::SHOW_PATH, 'TypeController@show')
                    ->name(TypeController::SHOW_NAME)->middleware('auth');
                Route::post(TypeController::STORE_PATH, 'TypeController@store')
                    ->name(TypeController::STORE_NAME)->middleware('auth');
            }
        );
        Route::namespace('Products\\Controllers\\')->group(
            function () {
                Route::post(ProductController::STORE_PATH, 'ProductController@store')
                    ->name(ProductController::STORE_NAME)->middleware('auth', 'productStore');
                Route::patch(ProductController::UPDATE_PATH, 'ProductController@update')
                    ->name(ProductController::UPDATE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::UPDATE_PRODUCT_PRICE
                    );
            }
        );
        Route::namespace('Products\\Controllers\\')->group( // The Inventory Controller is a Product concern.
            function () {
                Route::get(InventoryController::INDEX_PATH, 'InventoryController@index')
                    ->name(InventoryController::INDEX_NAME)->middleware('auth');
                Route::get(InventoryController::SHOW_PATH, 'InventoryController@show')
                    ->name(InventoryController::SHOW_NAME)->middleware('auth');
                Route::patch(InventoryController::UPDATE_PATH, 'InventoryController@update')
                    ->name(InventoryController::UPDATE_NAME)->middleware('auth');
            }
        );
        Route::namespace('Carts\\Controllers\\')->group(
            function () {
                Route::get(\App\Carts\Controllers\CartController::INDEX_PATH, 'CartController@index')
                    ->name(\App\Carts\Controllers\CartController::INDEX_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::get(\App\Carts\Controllers\CartController::SHOW_PATH, 'CartController@show')
                    ->name(\App\Carts\Controllers\CartController::SHOW_NAME)->middleware('auth');
                Route::post(\App\Carts\Controllers\CartController::STORE_PATH, 'CartController@store')
                    ->name(\App\Carts\Controllers\CartController::STORE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::delete(\App\Carts\Controllers\CartController::DESTROY_PATH, 'CartController@destroy')
                    ->name(\App\Carts\Controllers\CartController::DESTROY_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::patch(\App\Carts\Controllers\CartController::UPDATE_PATH, 'CartController@update')
                    ->name(\App\Carts\Controllers\CartController::UPDATE_NAME)->middleware('auth');
            }
        );
        Route::namespace('Carts\\Controllers\\')->group(
            function () {
                Route::post(\App\Carts\Controllers\PendingSaleController::STORE_PATH, 'PendingSaleController@store')
                    ->name(\App\Carts\Controllers\PendingSaleController::STORE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::delete(
                    \App\Carts\Controllers\PendingSaleController::DESTROY_PATH,
                    'PendingSaleController@destroy'
                )
                    ->name(\App\Carts\Controllers\PendingSaleController::DESTROY_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
            }
        );
    }
);
