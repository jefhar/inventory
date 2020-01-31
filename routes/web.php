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
use App\Products\Controllers\ProductsController;
use App\Types\Controllers\TypesController;
use App\WorkOrders\Controllers\ClientsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
            }
        );

        Route::namespace('WorkOrders\\Controllers\\')->group(
            function () {
                Route::resource('workorders', 'WorkOrdersController')->only(
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
                Route::get(ClientsController::SHOW_PATH, 'ClientsController@show')
                    ->name(ClientsController::SHOW_NAME)->middleware('auth');
            }
        );
        Route::namespace('Types\\Controllers\\')->group(
            function () {
                Route::get(TypesController::CREATE_PATH, 'TypesController@create')
                    ->name(TypesController::CREATE_NAME)->middleware('auth');
                Route::delete(TypesController::DESTROY_PATH, 'TypesController@destroy')
                    ->name(TypesController::DESTROY_NAME)->middleware('auth');
                Route::get(TypesController::INDEX_PATH, 'TypesController@index')
                    ->name(TypesController::INDEX_NAME)->middleware('auth');
                Route::get(TypesController::SHOW_PATH, 'TypesController@show')
                    ->name(TypesController::SHOW_NAME)->middleware('auth');
                Route::post(TypesController::STORE_PATH, 'TypesController@store')
                    ->name(TypesController::STORE_NAME)->middleware('auth');
            }
        );
        Route::namespace('Products\\Controllers\\')->group(
            function () {
                Route::post(ProductsController::STORE_PATH, 'ProductsController@store')
                    ->name(ProductsController::STORE_NAME)->middleware('auth', 'productStore');
                Route::patch(ProductsController::UPDATE_PATH, 'ProductsController@update')
                    ->name(ProductsController::UPDATE_NAME)->middleware(
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
                Route::get(\App\Carts\Controllers\CartsController::INDEX_PATH, 'CartsController@index')
                    ->name(\App\Carts\Controllers\CartsController::INDEX_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::get(\App\Carts\Controllers\CartsController::SHOW_PATH, 'CartsController@show')
                    ->name(\App\Carts\Controllers\CartsController::SHOW_NAME)->middleware('auth');
                Route::post(\App\Carts\Controllers\CartsController::STORE_PATH, 'CartsController@store')
                    ->name(\App\Carts\Controllers\CartsController::STORE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::delete(\App\Carts\Controllers\CartsController::DESTROY_PATH, 'CartsController@destroy')
                    ->name(\App\Carts\Controllers\CartsController::DESTROY_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::patch(\App\Carts\Controllers\CartsController::UPDATE_PATH, 'CartsController@update')
                    ->name(\App\Carts\Controllers\CartsController::UPDATE_NAME)->middleware('auth');
            }
        );
        Route::namespace('Carts\\Controllers\\')->group(
            function () {
                Route::post(\App\Carts\Controllers\PendingSalesController::STORE_PATH, 'PendingSalesController@store')
                    ->name(\App\Carts\Controllers\PendingSalesController::STORE_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
                Route::delete(
                    \App\Carts\Controllers\PendingSalesController::DESTROY_PATH,
                    'PendingSalesController@destroy'
                )
                    ->name(\App\Carts\Controllers\PendingSalesController::DESTROY_NAME)->middleware(
                        'auth',
                        'permission:' . UserPermissions::MUTATE_CART
                    );
            }
        );
    }
);
