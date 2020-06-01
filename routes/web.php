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
use App\Carts\Controllers\CartController;
use App\Carts\Controllers\PendingSaleController;
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
                Route::resource('dashboard', 'DashboardController')->middleware('auth');
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
                Route::get(CartController::INDEX_PATH, 'CartController@index')
                    ->name(CartController::INDEX_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::get(CartController::SHOW_PATH, 'CartController@show')
                    ->name(CartController::SHOW_NAME)->middleware('auth');
                Route::post(CartController::STORE_PATH, 'CartController@store')
                    ->name(CartController::STORE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::delete(CartController::DESTROY_PATH, 'CartController@destroy')
                    ->name(CartController::DESTROY_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::patch(CartController::UPDATE_PATH, 'CartController@update')
                    ->name(CartController::UPDATE_NAME)->middleware('auth');
            }
        );
        Route::namespace('Carts\\Controllers\\')->group(
            function () {
                Route::post(PendingSaleController::STORE_PATH, 'PendingSaleController@store')
                    ->name(PendingSaleController::STORE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::delete(
                    PendingSaleController::DESTROY_PATH,
                    'PendingSaleController@destroy'
                )
                    ->name(PendingSaleController::DESTROY_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
            }
        );
    }
);
