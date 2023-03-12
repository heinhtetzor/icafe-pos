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

Route::get('/mobile-server/start', 'Api\MobileServerController@start');

// Expense starts
Route::get('/expenses/{id}/getExpenseItems', 'Api\Expense\ExpenseController@getExpenseItems');

//get expense summary
Route::get('/expenses/getSummary/{date}', 'Api\Expense\ExpenseController@getSummary');
Route::get('/expenses/getSummary', 'Api\Expense\ExpenseController@getSummary');

Route::get('/expenses/getItem/{id}', 'Api\Expense\ExpenseController@getItem');
Route::post('/expenses/addExpenseItem', 'Api\Expense\ExpenseController@addExpenseItem');
Route::get('/expenses/getExpense/{expenseId}', 'Api\Expense\ExpenseController@getExpense');

Route::post('/expenses/confirm/{expenseId}', 'Api\Expense\ExpenseController@confirmExpense');


Route::post('/expenses/deleteExpenseItem', 'Api\Expense\ExpenseController@deleteExpenseItem');

//menus 
Route::get('/menus', 'Api\Menu\MenuController@index');
Route::get('/menu-groups', 'Api\MenuGroup\MenuGroupController@index');

//expense api ends

// Orders starts
Route::get('/test', 'Api\Waiter\WaiterController@index');

//get waitersd
Route::get('/getWaiters', 'Api\Waiter\WaiterController@index');

//get tables
Route::get('/tables', 'Api\Table\TableController@tables');

//get menus
Route::get('/getMenusByMenuGroup', 'Api\Menu\MenuController@getMenusByMenuGroup');

//submit order
Route::post('/submitOrder/{tableId}/{waiterId}', 'Api\Order\OrderController@submitOrder');

//add order for express 
Route::post('/addOrderMenu', 'Api\Order\OrderController@addOrderMenu');

//cancel order menu (triggered from pos detail order view)
Route::post('/orderMenus/cancel/{orderMenuId}/{cancelQuantity}', 'Api\Order\OrderController@cancelOrderMenu');

//make foc
Route::post('/orderMenus/makeFoc/{orderMenuId}', 'Api\Order\OrderController@makeFoc');

//undo ordermenu options such as foc
Route::post('/orderMenus/undoOption/{orderMenuId}', 'Api\Order\OrderController@undoOption');

//pay bill
Route::get('/payBill/{orderId}/{waiterId}/{customerId}/{paidAmount}/{printBill}', 'Api\Order\OrderController@payBill');

//serve to customer
Route::get('/serveToCustomer/{orderMenuId}', 'Api\Order\OrderController@serveToCustomer');
Route::get('/serveAllToCustomer/{menuGroupId}', 'Api\Order\OrderController@serveAllToCustomer');

//get order summary for day
Route::get('/orders/getSummary/{date}', 'Api\Order\OrderController@getSummary');
Route::get('/orders/getSummary', 'Api\Order\OrderController@getSummary');
// get order summary for day by orderId
Route::get('/orders/getSummaryByOrder/{id}', 'Api\Order\OrderController@getSummaryByOrder');


//stock menu creation
Route::get('/stock-menus', 'Api\Menu\MenuController@getStockMenus');

//waiter - get current order
Route::get('/waiter/{orderId}/orders', 'Api\Order\OrderController@show');

//kitchen - get yellow order from related menu groups
Route::get('/kitchen/{kitchenId}/orders', 'Api\Order\OrderController@getKitchenOrders');


//table pos view
Route::prefix('table-statuses')->group(function () {
    Route::get('/', 'Api\Table\TableStatusController@index');
    Route::get('/{id}', 'Api\Table\TableStatusController@show');
});
