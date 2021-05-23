<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderMenu;
use App\Setting;
use App\MenuGroup;
use DB;

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

class PrintController extends Controller
{
    public function printOrder (Order $order)
    {                
        $orderMenus = $order->order_menus()
                     ->join('menus', 'menus.id', 'order_menus.menu_id')
                     ->join('menu_groups', 'menu_groups.id', 'menus.menu_group_id')                    
                     ->selectRaw("order_menus.id as id, menu_id, SUM(quantity) as quantity, menus.menu_group_id as menu_group_id, order_menus.price as price, is_foc, order_menus.status as status, order_menus.created_at as created_at, menu_groups.name as menu_group_name")                
                     ->groupBy('menu_id', 'is_foc')                     
                     ->with('menu') 
                     ->get();

        foreach ($orderMenus->groupBy('menu_group_name') as $orderMenusGroupedBy)
        {
            //print each page
        }
        
        $printer_connector = Setting::getPrinterConnector();        

    	$connector = new FilePrintConnector($printer_connector);
    	$printer = new Printer($connector);
    	$printer->text("HEllo wo rld\n");
    	$printer->cut();
    	$printer->close();
    	
    	dd($printer);
    }
}
