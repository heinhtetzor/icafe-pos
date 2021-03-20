<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Table;
use App\Order;
use App\OrderMenu;
use App\Kitchen;
use App\MenuGroup;
use App\TableStatus;
use Illuminate\Http\Request;
use App\Http\Traits\OrderFunctions;

class OrderController extends Controller
{
    use OrderFunctions;
    
    function submitOrder (Request $request, $tableId, $waiterId) {
        // params all Int
        // ===========
        // waiterId
        // tableId

        // needed data from Request as array
        // ============
        // menuId
        // price
        // status
        // quantity
        

        //early return if orderMenus is empty
        if(count($request->get('orderMenus'))===0) {
            return ["isOk"=>FALSE];
        }
        $table = Table::findorfail($tableId);
        $orderId=null;
        if($table->table_status->isTableFree()) {
            //create new order
            $orderData = [
                'status'=>0,
                'table_id'=>$tableId
            ];
        
            $order = Order::create($orderData);
            $tableStatus = TableStatus::where('table_id', $tableId)->update([
                'status'=>1,
                'order_id'=>$order->id
            ]);
            
            $orderId = $order->id;
        }
        else {
            //bind with 
            $orderId = $table->table_status->order_id;
        }

        //for loop order_menus

        foreach($request->get('orderMenus') as $orderMenu) {
            if ($orderMenu["quantity"] < 1) {
                continue;
            }
            OrderMenu::create([
                "menu_id"=> $orderMenu["menu_id"],
                "price"=>$orderMenu["price"],
                "status"=>0,
                "order_id"=>$orderId,
                "quantity"=>$orderMenu["quantity"],
                "waiter_id"=>$waiterId
            ]);
        }
        return ["isOk"=>TRUE, "orderMenus"=>$request->get('orderMenus')];
    }

    function makeFoc($orderMenuId)
    {
        OrderMenu::findorfail($orderMenuId)
        ->update([
                'is_foc' => 1,
                'price' => 0
                ]);
        return ["isOk" => TRUE];
    }

    function undoOption($orderMenuId)
    {
        $om = OrderMenu::findorfail($orderMenuId);
        $originalPrice = $om->menu->price;
        if ($om->is_foc === 1) {
            $om->update([
                'is_foc' => 0,
                'price' => $originalPrice
            ]);
            return ["isOk" => TRUE];
        }
        else {
            return ["isOk" => FALSE, "message" => "NOT FOC"];
        }
    }

    function payBill($orderId, $waiterId) {
        
        //string null set by javascript
        if($orderId === "null") {
            //early return if orderId is null
            return ["isOk"=>FALSE];
        }

        //TODO::check of all orders are served to customers 
        $order=Order::findorfail($orderId);
        foreach($order->order_menus as $om) {
            if($om->status===0) {
                return ["isOK"=>FALSE];                
            }
        }


        //change status and add waiterId
        Order::findorfail($orderId)->update([
            "status"=>1,
            "waiter_id"=>$waiterId
        ]);
        //change table status
        TableStatus::where('order_id', $orderId)->update([
            "status"=>0,
            "order_id"=>null
        ]);
        return ["isOk"=>TRUE];
    }

    function serveToCustomer($orderMenuId) {
        //change status 
        OrderMenu::findorfail($orderMenuId)->update([
            "status"=>1
        ]);
        return ["isOk"=>TRUE];
    }

    function serveAllToCustomer($menuGroupId) {
        //TODO
        OrderMenu::whereHas('menu', function($q) use ($menuGroupId) {
            $q->where('menu_group_id', $menuGroupId);
        })
        ->where('status', 0)
        ->update([
            "status"=>1
        ]);
        return ["isOk"=>TRUE];        
    }

    //for waiter and admin pos views
    //details order view
    function show($id) {        
        $order=Order::findorfail($id);
        $orderMenus=$this->getOrderMenusList($order);        
        $total=$orderMenus->sum(function($t) {
            return $t->quantity*$t->price;
        });
        return response()->json([
            "order"=>$order,
            "orderMenus"=>$orderMenus,
            "total"=>$total
        ]);    
    }

    function getKitchenOrders($id) {
        $kitchen=Kitchen::findorfail($id);
        $arr=[];
        foreach($kitchen->menu_groups as $kmg) {
            array_push($arr, [                
                    "menuGroup"=>$kmg,
                    "orderMenus"=>$this->getOrderMenusByMenuGroup($kmg->id)           
            ]);
        }
        return $arr;
    }
}
