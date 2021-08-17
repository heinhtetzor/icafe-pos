<?php
namespace App\Http\Traits;
use App\Order;
use App\TableStatus;
use App\OrderMenu;
use Illuminate\Support\Facades\DB;

trait OrderFunctions {
    
    function getActiveOrder($tableId) {
        $table_statuses=TableStatus::where('table_id', $tableId)->where('status', 1)->get();
        if(count($table_statuses)<1) {
            return;
        }
        $table_status=$table_statuses[0];
  
        return $table_status->order;
    }

    function getSummaryByOrder ($id)
    {
        //for summary panel
        $orderMenuGroups=DB::table('order_menus')
        ->join('menus', 'order_menus.menu_id', '=', 'menus.id')
        ->join('menu_groups', 'menus.menu_group_id', '=', 'menu_groups.id')
        ->join('orders', 'orders.id', '=', 'order_menus.order_id')                      
        ->selectRaw('menu_groups.id as id, menu_groups.name as name, SUM(order_menus.quantity) as quantity, SUM(order_menus.quantity*order_menus.price) as total')
        ->where('orders.id', '=', $id)                              
        ->groupBy('menu_groups.id')
        ->get();  
        return $orderMenuGroups;    
    }

    //used in pos cart view
    //used in print bill
    static function getOrderMenusGrouped(Order $order) {
        return $order->order_menus()
                     ->selectRaw("id, menu_id, SUM(quantity) as quantity, price, is_foc, status, created_at")
                     ->groupBy('menu_id', 'is_foc')                     
                     ->with('menu', 'menu.menu_group') 
                     ->get();
    }

    function getOrderMenusList(Order $order) {
                
        return OrderMenu::where('order_id', $order->id)
                        ->with('menu', 'waiter')  
                        ->orderBy('created_at', 'DESC')                  
                        ->simplePaginate(30);
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
        ->where('order_menus.status', 0)
        ->orderBy('order_menus.status', 'ASC')
        ->orderBy('order_menus.created_at', 'DESC')
        ->groupBy('order_menus.menu_id', 'order_menus.order_id', 'order_menus.status')                
        ->get();        
        return $orderMenus;
    }
}
