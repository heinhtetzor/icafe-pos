<?php
namespace App\Http\Traits;
use App\Order;
use App\TableStatus;
use App\OrderMenu;
trait OrderFunctions {
    
    function getActiveOrder($tableId) {
        $table_statuses=TableStatus::where('table_id', $tableId)->where('status', 1)->get();
        if(count($table_statuses)<1) {
            return;
        }
        $table_status=$table_statuses[0];
  
        return $table_status->order;
    }

    //used in pos cart view
    function getOrderMenusGrouped(Order $order) {
        return $order->order_menus()
                     ->selectRaw("id, menu_id, SUM(quantity) as quantity, price, is_foc, status, created_at")
                     ->groupBy('menu_id', 'is_foc')                     
                     ->with('menu') 
                     ->get();
    }

    function getOrderMenusList(Order $order) {
                
        return OrderMenu::where('order_id', $order->id)
                        ->with('menu', 'waiter')  
                        ->orderBy('created_at', 'DESC')                  
                        ->get();
    }

    //used by kithen home view
    function getOrderMenusByMenuGroup($id) {
        // $orderMenus=OrderMenu::where('order_id', 7)->get();
        $orderMenus=OrderMenu::whereHas('menu', function($q) use ($id) {
            $q->where('menu_group_id', $id);
        })
        ->join('menus', 'menus.id', '=', 'order_menus.menu_id')
        ->join('waiters', 'waiters.id', '=', 'order_menus.waiter_id')
        ->join('orders', 'orders.id', '=', 'order_menus.order_id')
        ->join('tables', 'tables.id', '=', 'orders.table_id')
        ->selectRaw('order_menus.id as id, 
                     order_menus.status as status,
                     menus.name as menu,
                     orders.id as `order`,
                     tables.name as `table`,
                     waiters.name as waiter,
                     SUM(order_menus.quantity) as quantity')
        // ->with('menu', 'waiter', 'order', 'order.table')
        ->orderBy('order_menus.status', 'ASC')
        ->orderBy('order_menus.created_at', 'DESC')
        ->groupBy('order_menus.menu_id', 'order_menus.order_id', 'order_menus.status')        
        ->get();        
        return $orderMenus;
    }
}