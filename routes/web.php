<?php

use App\Http\Controllers\TableController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

Route::get('/', function () {
    return view('home');
});
Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
});
Route::get('/backup', function () {
    try {
        Artisan::call('backup:data');
    }
    catch (Exception $e) {        
        return response()->json([
            "message" => "Error"
        ], 500);
    }
    return response()->json([
        "isOk" => TRUE,
        "message" => "Backup Completed"
    ]);
    
});


Route::get('/software-upgrade', function () {
    try {
        exec("cd ../ git reset --hard HEAD && git pull");
    }
    catch (Exception $e) {
        return response()->json([
            "message" => "Error upgrading",
            "details" => $e->getMessage()
        ], 500);
    }

    return response()->json([
        "isOk" => TRUE,
        "message" => "Software Upgrade Completed",        
    ]);

});

Route::get('/database-upgrade', function () {
    try {
        exec("cd ../ && php artisan migrate --force");
    }
    catch (Exception $e) {
        return response()->json([
            "message" => "Error upgrading",
            "details" => $e->getMessage()
        ], 500);
    }

    return response()->json([
        "isOk" => TRUE,
        "message" => "Database Upgrade Completed",        
    ]);

});

Route::get('/composer-upgrade', function () {
    try {
        exec("cd ../ && composer install");
    }
    catch (Exception $e) {
        return response()->json([
            "message" => "Error upgrading",
            "details" => $e->getMessage()
        ], 500);
    }

    return response()->json([
        "isOk" => TRUE,
        "message" => "Dependency Upgrade Completed",        
    ]);

});

Route::group(['prefix' => 'admin'], function () {
    
    Route::get('/login', 'AdminHomeController@showAdminLogin')->name('admin.showLogin');
    Route::post('/loginPost', 'AdminHomeController@adminLogin')->name('admin.login');
    Route::post('/logout', 'AdminHomeController@adminLogout')->name('admin.logout');
    
    Route::group(['middleware' => 'adminAccountAuth'], function () {
        Route::get('/', 'AdminHomeController@admin')->name('admin.home');

        //inventory
        Route::get('/stockmenus', 'StockMenuController@index')->name('stockmenus.index');
        Route::get('/stockmenus/{stockMenu}', 'StockMenuController@show')->name('stockmenus.show');

        //expense module
        Route::get('/expenses', 'ExpenseController@index')->name('expenses.index');
        Route::get('/expenses/create', 'ExpenseController@create')->name('expenses.create');
        Route::get('/expenses/edit/{id}', 'ExpenseController@edit')->name('expenses.edit');
        Route::get('/expenses/{id}', 'ExpenseController@show')->name('expenses.show');
        Route::post('/expenses/store', 'ExpenseController@store')->name('expenses.store');
        Route::delete('/expenses/destroy/{id}', 'ExpenseController@destroy')->name('expenses.destroy');

        // express pos
        Route::get('/express', 'ExpressHomeController@home')->name('express.home');
        Route::get('/express/create', 'ExpressHomeController@create')->name('express.create');
        Route::get('/express/show/{id}', 'OrderController@show')->name('express.show');
        Route::post('/express/store', 'ExpressHomeController@store')->name('express.store');
        Route::delete('/express/destroy/{id}', 'OrderController@destroy')->name('express.destroy');

        Route::get('/tables', 'WaiterHomeController@home')->name('waiter.home');
        //go to POS instance
        //id=tableId


        Route::get('/tables', 'Waiter\WaiterController@tables')->name('client.waiter.tables');
        Route::get('/tables/{id}', 'Waiter\WaiterController@menus')->name('client.waiter.menus');

        Route::get('/accountmanagement', 'AdminHomeController@accountmanagement')->name('admin.accountmanagement');
        Route::get('/masterdatamanagement', 'AdminHomeController@masterdatamanagement')->name('admin.masterdatamanagement');

        Route::get('/pos/tables', 'AdminHomeController@tables')->name('admin.tables');
        

        Route::get('/pos/tables/{id}', 'AdminHomeController@pos')->name('admin.pos');
        Route::get('/pos/tables/{id}/orders', 'AdminHomeController@orders')->name('admin.pos.orders');

        //reporting
        Route::get('/reports', 'ReportController@index')->name('admin.reports');
        Route::get('/reports/day', 'OrderController@day')->name('admin.reports.day');
        Route::get('/reports/menus', 'ReportController@menus')->name('admin.reports.menus');
        
        Route::get('/reports/expenses', 'ExpenseController@index')->name('admin.reports.expenses');
        Route::get('/reports/items', 'ReportController@items')->name('admin.reports.items');

        Route::get('/reports/stock-menus', 'ReportController@stockMenus')->name('admin.reports.stock-menus');

        Route::get('/reports/profit-loss', 'ReportController@profitLossIndex')->name('admin.reports.profit-loss');
        Route::get('/reports/profit-loss/get-data-for-menu-groups-bar-chart', 'ReportController@getDataForMenuGroupsBarChart')->name('admin.reports.profit-loss.get-data-for-menu-groups-bar-chart');
        Route::get('/reports/profit-loss/get-data-for-daily-line-chart', 'ReportController@getDataForDailyLineChart')->name('admin.reports.profit-loss.get-data-for-daily-line-chart');
        

        // extra order routes
        //for today orders
        Route::get('/orders/day/today', 'OrderController@day')->name('orders.today');
        Route::get('/orders/calendar', 'OrderController@calendar')->name('orders.calendar');

        Route::get('/settings', 'SettingController@index')->name('settings.index');
        Route::get('/settings/passcode', 'SettingController@passcode')->name('settings.passcode');
        Route::post('/settings/savePasscode', 'SettingController@savePasscode')->name('settings.savePasscode');

        Route::get('/settings/shop', 'SettingController@shop')->name('settings.shop');
        Route::post('/settings/saveShop', 'SettingController@saveShop')->name('settings.saveShop');

        Route::get('/settings/getAll', 'SettingController@getAll')->name('settings.getAll');
        
        Route::get('/settings/download-backup-file', 'SettingController@downloadBackupFile')->name('settings.download-backup-file');
        
        //print functions
        Route::get('/orders/{order}/printSummary', 'PrintController@printOrderSummary')->name('orders.printOrderSummary');
        Route::get('/orders{order}/printBill', 'PrintController@printOrderBill')->name('orders.printOrderBill');
        Route::post('/reports/menus/printMenuReport', 'PrintController@printMenuReport')->name('reports.printMenuReport');
        Route::resource('/orders', 'OrderController');

        Route::resource('/tables', 'TableController');
        Route::resource('/items', 'ItemController');
        Route::resource('/tablegroups', 'TableGroupController');
        Route::resource('/menugroups', 'MenuGroupController');
        Route::resource('/menus', 'MenuController');
        Route::resource('/waiters', 'WaiterController');
        Route::resource('/admin_accounts', 'AdminAccountController');
        Route::resource('/kitchens', 'KitchenController');
    });

});
Route::group(['prefix' => 'waiter'], function() {

    Route::get('/login', 'WaiterHomeController@showWaiterLogin')->name('waiter.showLogin');
    Route::post('/loginPost', 'WaiterHomeController@waiterLogin')->name('waiter.login');
    Route::post('/logout', 'WaiterHomeController@waiterLogout')->name('waiter.logout');

    
    Route::group(['middleware' => 'waiterAccountAuth'], function () {
        Route::get('/', 'WaiterHomeController@home')->name('waiter.home');
        //go to POS instance
        //id=tableId
        Route::get('/{id}/pos', 'WaiterHomeController@pos')->name('waiter.pos');

        //go to order details
        Route::get('/{id}/orders', 'WaiterHomeController@orders')->name('waiter.orders');

        Route::get('/tables', 'Waiter\WaiterController@tables')->name('client.waiter.tables');
        Route::get('/tables/{id}', 'Waiter\WaiterController@menus')->name('client.waiter.menus');
    });
});
Route::group(['prefix' => 'kitchen'], function() {

    Route::get('/login', 'KitchenHomeController@showKitchenLogin')->name('kitchen.showLogin');
    Route::post('/loginPost', 'KitchenHomeController@kitchenLogin')->name('kitchen.login');
    Route::post('/logout', 'KitchenHomeController@kitchenLogout')->name('kitchen.logout');

    Route::group(['middleware' => 'kitchenAccountAuth'], function () {
        Route::post('/adjustPanelSize', 'KitchenHomeController@adjustPanelSize')->name('kitchen.adjustPanelSize');
        Route::get('/', 'KitchenHomeController@home')->name('kitchen.home');
    });
});
