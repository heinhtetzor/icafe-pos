<?php

namespace App\Http\Controllers\Api\Order;
use App\Http\Controllers\Controller;
use App\Table;
use App\Order;
use App\OrderMenu;
use App\Kitchen;
use App\MenuGroup;
use App\Setting;
use App\TableStatus;
use Illuminate\Http\Request;
use App\Http\Traits\OrderFunctions;
use App\Menu;
use App\Services\PrintService;
use App\StockMenu;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use SteveNay\MyanFont\MyanFont;
use File;

use Mike42\Escpos\EscposImage;

class OrderController extends Controller
{
    use OrderFunctions;
    
    function submitOrder (Request $request, $tableId, $waiterId) {        
        try {
            DB::beginTransaction();
            $store_id = Auth()->guard('admin_account')->user()->store_id ?? Auth()->guard('waiter')->user()->store_id;
            //early return if orderMenus is empty
            if(count($request->get('orderMenus'))===0) {
                return ["isOk"=>FALSE];
            }
            $table = Table::lockForUpdate()->findorfail($tableId);
            $orderId=null;
            
            if($table->table_status->isTableFree()) {
                //create new order
                $orderData = [
                    'status'=>0,
                    'table_id'=>$tableId,
                    'invoice_no'=>Order::generateInvoiceNumber(),
                    'store_id'=>$store_id
                ];
            
                $order = Order::create($orderData);
                
                $tableStatus = TableStatus::where('table_id', $tableId)->update([
                    'status'=>1,
                    'order_id'=>$order->id,
                    'waiter_id' => $waiterId,
                ]);
                
                $orderId = $order->id;
            }
            else {
                //bind with 
                $orderId = $table->table_status->order_id;
            }

            //for loop order_menus
            // $orderMenu is from request 
            foreach($request->get('orderMenus') as $orderMenu) {
                if ($orderMenu["quantity"] < 1) {
                    continue;
                }
                //if menu id already existed in order
                //increment existing ordermenu            
                $order = Order::findorfail($orderId);
                $order_menu = $order->order_menus()->where('menu_id', $orderMenu["menu_id"])->first();
                
                if (is_null($order_menu)) { //new
                    //if menu id is new create new ordermenu
                    $order_menu = OrderMenu::create([
                        "menu_id"=> $orderMenu["menu_id"],
                        "price"=>$orderMenu["price"],
                        "status"=>0,
                        "order_id"=>$orderId,
                        "quantity"=>$orderMenu["quantity"],
                        "waiter_id"=>$waiterId
                    ]);                    
                } else {                                
                    $order_menu->quantity = $order_menu->quantity + (int) $orderMenu["quantity"];
                    $order_menu->save();
                }          
                

                //adjust stock
                $menu = Menu::findOrFail($orderMenu["menu_id"]);

                if ($menu->stock_menu()->exists() && $menu->stock_menu->status == StockMenu::STATUS_ACTIVE) {
                    $stock_menu = $menu->stock_menu;
                    if ($stock_menu->balance == 0) {
                        DB::rollBack();
                        return response()->json([
                            "success" => FALSE,
                            "message" => "ပစ္စည်းမရှိတော့ပါ"
                        ]);
                    }
    
                    $stock_menu->stockMenuEntries()->create([
                        "order_menu_id" => $order_menu->id,
                        "cost" => $orderMenu['price'],
                        "in" => 0,
                        "out" => $orderMenu['quantity'],
                        "balance" => $stock_menu->balance - (int) $orderMenu['quantity']
                    ]);
    
                    $stock_menu->balance -= (int) $orderMenu['quantity'];
                    $stock_menu->save();
                }
            }
            PrintService::printOrderSlipTable($order, $waiterId, $request->get('printOrderMenus'));

            DB::commit();

            return ["isOk"=>TRUE, "orderMenus"=>$request->get('orderMenus')];
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json([
            "message" => $e->getMessage()
            ]);
        }
        

    }

    function addOrderMenu (Request $request)
    {
        try {    
            DB::beginTransaction();


            $orderMenu = OrderMenu::create([
                'waiter_id' => $request->waiterId,
                'menu_id' => $request->menuId,
                'price' => $request->menuPrice, 
                'order_id' => $request->orderId,
                'quantity' => $request->quantity,
                'status' => 0,
                'is_foc' => 0
            ]);

            $menu = Menu::findOrFail($request->menuId);
            if ($menu->stock_menu()->exists() && $menu->stock_menu->status == StockMenu::STATUS_ACTIVE) {
                $stock_menu = $menu->stock_menu;
                if ($stock_menu->balance == 0) {
                    DB::rollBack();
                    return response()->json([
                        "success" => FALSE,
                        "message" => "ပစ္စည်းမရှိတော့ပါ"
                    ]);
                }

                $stock_menu->stockMenuEntries()->create([
                    "order_menu_id" => $orderMenu->id,
                    "cost" => $orderMenu->price,
                    "in" => 0,
                    "out" => $orderMenu->quantity,
                    "balance" => $stock_menu->balance - (int) $orderMenu->quantity
                ]);

                $stock_menu->balance -= (int) $orderMenu->quantity;
                $stock_menu->save();
            }


            PrintService::printOrderSlipExpress($orderMenu);

            DB::commit();

            return response()->json([
                "success" => TRUE,
                "orderMenu" => $orderMenu
            ]);
        }
        catch (Exception $e) {            
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    function getSummary (Request $request)
    {
        $fromTime = null;
        $toTime = null;

        $isToday = false;

        // dd($request->date);

        if($request->date) {
            // $from=date($request->date)->startOfDay(); 
            $from=explode(" - ", $request->date)[0];
            $to=explode(" - ", $request->date)[1];
            $fromTime=Carbon::parse($from)->startOfDay();
            $toTime=Carbon::parse($to)->endOfDay();
        }
        else {
            $fromTime=now()->startOfDay();
            $toTime=now()->endOfDay();
            $isToday=TRUE;
        }

        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $orderMenuGroups=DB::table('order_menus')
                      ->join('menus', 'order_menus.menu_id', '=', 'menus.id')
                      ->join('menu_groups', 'menus.menu_group_id', '=', 'menu_groups.id')
                      ->join('orders', 'orders.id', '=', 'order_menus.order_id')                      
                      ->selectRaw('menu_groups.id as id, menu_groups.name as name, SUM(order_menus.quantity) as quantity, SUM(order_menus.quantity*order_menus.price) as total')
                      ->where('orders.status', '=', '1')
                      ->where('orders.store_id', $store_id)
                      ->whereBetween('orders.created_at', [$fromTime, $toTime])
                      ->groupBy('menu_groups.id')
                      ->get();  

        return response()->json([
            "orderMenuGroups" => $orderMenuGroups
        ]);
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
        if ($om->is_foc == 1) {
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

    function payBill($orderId, $waiterId, $printBill=false) {
        // dd($printBill);
        
        //string null set by javascript
        if($orderId === "null") {
            //early return if orderId is null
            return ["isOk"=>FALSE];
        }



        //TODO::check of all orders are served to customers 
        $order=Order::lockForUpdate()->findorfail($orderId);
        //disabled to pay bill without kitchen confirmation
        
        // foreach($order->order_menus as $om) {
        //     if($om->status==0) {
        //         return ["isOK"=>FALSE];                
        //     }
        // }
        $order_menu_total = $order->order_menus->sum(function ($q) {
            return $q->quantity * $q->price;
        });


        //change status and add waiterId
        Order::findorfail($orderId)->update([
            "status"=>1,
            "waiter_id"=>$waiterId,
            "total"=>$order_menu_total
        ]);
        //change table status
        TableStatus::where('order_id', $orderId)->update([
            "status"=>0,
            "order_id"=>null,
            "waiter_id"=>null
        ]);
        if ($printBill == "true")
        {
            $order = Order::findorfail($orderId);
            PrintService::printOrderBill($order);
        }

        return ["isOk"=>TRUE];
    }

    function serveToCustomer($orderMenuId) {
        //change status 
        OrderMenu::findorfail($orderMenuId)->update([
            "status"=>1
        ]);
        return ["isOk"=>TRUE];
    }

    function cancelOrderMenu($orderMenuId, $cancelQuantity) {
        try {
            DB::beginTransaction();

            $orderMenu = OrderMenu::findorfail($orderMenuId);
            $orderId = $orderMenu->order_id;
            //mot allowed user to cancel if it is already served to customer
            if ($orderMenu->status === 1) {
                return ["isOk"=>FALSE];
            }
            
            if ((int) $cancelQuantity > $orderMenu->quantity) {
                throw new Exception("Cancel quantity is more than orders");
            }

            $newQuantity = $orderMenu->quantity - $cancelQuantity;
            
            $orderMenu->quantity = $newQuantity;
            $orderMenu->save();

            $stock_menu_entry = $orderMenu->stockEntry;

            if (!is_null ($stock_menu_entry)) {
                $stock_menu_entry->out -= (int) $cancelQuantity;
                $stock_menu_entry->save();

                if ($stock_menu_entry->out == 0) {
                    $stock_menu_entry->delete();
                }

                $stock_menu = $orderMenu->menu->stock_menu;
                $stock_menu->balance += (int) $cancelQuantity;
                $stock_menu->save();
            }

            $order = $orderMenu->order;

            if ($order->status == Order::SUBMITTED && $cancelQuantity > 0) {
                //log deleted row
                $order->logDeletion([
                    "item_name" => $orderMenu->menu->name,
                    "price" => $orderMenu->price,
                    "quantity" => $cancelQuantity,
                    "deleted_at" => Carbon::now()->format('d-M-Y h:i A')
                ]);
            }

        
            if ($orderMenu->quantity == 0) {
                $orderMenu->delete();    
            }
            
            $order = Order::findorfail($orderId);
            
            if (count($order->order_menus) < 1) {            
                TableStatus::where('order_id', $orderId)->update([
                    "status"=>0,
                    "order_id"=>null
                ]);
                $order->delete();

                DB::commit();
                if ($order->table_id == 'express') {
                    return ["returnToExpress" => TRUE]; 
                }
                return ["returnToTables" => TRUE];
            }
            
            DB::commit();
            return ["isOk"=>TRUE];
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
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
