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

    function getOrderMenusByMenuGroup($id) {
        // $orderMenus=OrderMenu::where('order_id', 7)->get();
        $orderMenus=OrderMenu::whereHas('menu', function($q) use ($id) {
            $q->where('menu_group_id', $id);
        })
        ->with('menu', 'waiter')
        ->orderBy('created_at', 'DESC')
        ->get();
        // dd($orderMenus);
        return $orderMenus;
    }
}