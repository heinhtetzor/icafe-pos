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
        $table = Table::lockForUpdate()->findorfail($tableId);
        $orderId=null;
        
        if($table->table_status->isTableFree()) {
            //create new order
            $orderData = [
                'status'=>0,
                'table_id'=>$tableId,
                'invoice_no'=>Order::generateInvoiceNumber()
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
            //if menu id already existed in order
            //increment existing ordermenu            
            $order = Order::findorfail($orderId);
            $is_old = $order->order_menus()->where('menu_id', $orderMenu["menu_id"])->first();
            
            if (is_null($is_old)) { //new
                //if menu id is new create new ordermenu
                OrderMenu::create([
                    "menu_id"=> $orderMenu["menu_id"],
                    "price"=>$orderMenu["price"],
                    "status"=>0,
                    "order_id"=>$orderId,
                    "quantity"=>$orderMenu["quantity"],
                    "waiter_id"=>$waiterId
                ]);
            }
            if (!is_null($is_old)) { //old                
                $is_old->quantity = $is_old->quantity + (int) $orderMenu["quantity"];
                $is_old->save();
            }


        }
        return ["isOk"=>TRUE, "orderMenus"=>$request->get('orderMenus')];
    }

    function addOrderMenu (Request $request)
    {
        try {            
            $orderMenu = OrderMenu::create([
                'waiter_id' => $request->waiterId,
                'menu_id' => $request->menuId,
                'price' => $request->menuPrice, 
                'order_id' => $request->orderId,
                'quantity' => $request->quantity,
                'status' => 0,
                'is_foc' => 0
            ]);

            // only if menu grup is slip print enabled
            if ($orderMenu->menu->menu_group->print_slip == 1)
            {

                $printer_connector = Setting::getPrinterConnector();        
                $shop_infos = Setting::getShopInfo()->pluck('value');

                $connector = new FilePrintConnector($printer_connector);
                $printer = new Printer($connector);

                $width = 570;            
                $height = 200;

                $im = imagecreatetruecolor($width, $height);
                $white = imagecolorallocate($im, 255, 255, 255);
                $grey = imagecolorallocate($im, 128, 128, 128);
                $black = imagecolorallocate($im, 0, 0, 0);


                $font = realpath('fonts/zawgyi.ttf');

                $Y = 30;

                imagefilledrectangle($im, 0, 0, $width, $height, $white);
                $font_size = 20;
                
                imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg(Carbon::parse($orderMenu->created_at)->format('h:i A d-M-Y')));

                $Y += 50;            

                imagettftext($im, $font_size, 0, 10, $Y, $black, $font, MyanFont::uni2zg($orderMenu->menu->name));
                
                imagettftext($im, $font_size, 0, $width - 130, $Y, $black, $font, MyanFont::uni2zg($orderMenu->quantity));
                $Y += 50;

                $waiter = null;

                if (is_null($orderMenu->waiterId)) {
                    $waiter = "Express";
                }
                if (!is_null($orderMenu->waiterId)) {
                    $waiter = $orderMenu->waiter->name;
                }

                imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg($waiter));

                imagepng($im, "print-slip.png");            

                
                $img = EscposImage::load("print-slip.png");
                $printer -> bitImage($img);
                $printer -> cut();


                File::delete(public_path('print-slip.png'));
            }

            return response()->json([
                "orderMenu" => $orderMenu
            ]);
        }
        catch (Exception $e) {            
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

        $orderMenuGroups=DB::table('order_menus')
                      ->join('menus', 'order_menus.menu_id', '=', 'menus.id')
                      ->join('menu_groups', 'menus.menu_group_id', '=', 'menu_groups.id')
                      ->join('orders', 'orders.id', '=', 'order_menus.order_id')                      
                      ->selectRaw('menu_groups.id as id, menu_groups.name as name, SUM(order_menus.quantity) as quantity, SUM(order_menus.quantity*order_menus.price) as total')
                      ->where('orders.status', '=', '1')                      
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

    function payBill($orderId, $waiterId) {
        
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

    function cancelOrderMenu($orderMenuId, $cancelQuantity) {
        try {
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
        
            if ($orderMenu->quantity === 0) {
                $orderMenu->delete();    
            }
            
            $order = Order::findorfail($orderId);
            
            if (count($order->order_menus) < 1) {            
                TableStatus::where('order_id', $orderId)->update([
                    "status"=>0,
                    "order_id"=>null
                ]);
                $order->delete();
                return ["returnToTables" => TRUE];
            }
    
            return ["isOk"=>TRUE];
        }
        catch (Exception $e) {
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
