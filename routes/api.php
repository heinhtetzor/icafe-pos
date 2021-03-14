<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/test', 'Api\Waiter\WaiterController@getWaiters');

//get waitersd
Route::get('/getWaiters', 'Api\Waiter\WaiterController@getWaiters');

//get tables
Route::get('/getTables', 'Api\Table\TableController@getTables');

//get menus
Route::get('/getMenusByMenuGroup', 'Api\Menu\MenuController@getMenusByMenuGroup');

//submit order
Route::post('/submitOrder/{tableId}/{waiterId}', 'Api\Order\OrderController@submitOrder');

//pay bill
Route::get('/payBill/{orderId}/{waiterId}', 'Api\Order\OrderController@payBill');

//serve to customer
Route::get('/serveToCustomer/{orderMenuId}', 'Api\Order\OrderController@serveToCustomer');
Route::get('/serveAllToCustomer/{menuGroupId}', 'Api\Order\OrderController@serveAllToCustomer');


//waiter - get current order
Route::get('/waiter/{orderId}/orders', 'Api\Order\OrderController@show');

//kitchen - get yellow order from related menu groups
Route::get('/kitchen/{kitchenId}/orders', 'Api\Order\OrderController@getKitchenOrders');