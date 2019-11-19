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

use App\Admin\Controllers\AjaxSearchController;

Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('workorders', 'WorkOrdersController')->only(
    [
        'create',
        'show',
        'store',
    ]
)->middleware('auth');
Route::get(AjaxSearchController::SHOW_PATH, 'AjaxSearchController@show')
->name(AjaxSearchController::SHOW_NAME);
