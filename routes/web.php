<?php

use App\Http\Controllers\TableController;
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
Route::get('/upgrade', function () {
    $git_pull = exec("cd ../ && git pull");
    $composer = exec("cd ../ && composer install");
    $migration = exec("cd ../ && php artisan migrate");
    // $process->run();

    // if (!$process->isSuccessful()) {
    //     throw new ProcessFailedException($process);
    // }

    return redirect('/admin')->with('msg', $git_pull);
});
Route::group(['prefix' => 'admin'], function () {
    
    Route::get('/login', 'AdminHomeController@showAdminLogin')->name('admin.showLogin');
    Route::post('/loginPost', 'AdminHomeController@adminLogin')->name('admin.login');
    Route::post('/logout', 'AdminHomeController@adminLogout')->name('admin.logout');
    
    Route::group(['middleware' => 'adminAccountAuth'], function () {
        Route::get('/', 'AdminHomeController@admin')->name('admin.home');

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

        // extra order routes
        //for today orders
        Route::get('/orders/day/today', 'OrderController@day')->name('orders.today');
        Route::get('/orders/calendar', 'OrderController@calendar')->name('orders.calendar');

        Route::get('/settings', 'SettingController@index')->name('settings.index');
        Route::post('/settings/save', 'SettingController@save')->name('settings.save');
        
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