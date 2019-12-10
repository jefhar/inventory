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
                Route::get(TypesController::SHOW_PATH, 'TypesController@show')
                    ->name(TypesController::SHOW_NAME)->middleware('auth');
            }
        );
        Route::namespace('Products\\Controllers\\')->group(
            function () {
                Route::post(ProductsController::STORE_PATH, 'ProductsController@store')
                    ->name(ProductsController::STORE_NAME)->middleware('auth', 'productStore');
            }
        );
    }
);
