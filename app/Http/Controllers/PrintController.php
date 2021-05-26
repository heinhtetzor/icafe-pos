<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderMenu;
use App\Setting;
use App\MenuGroup;
use DB;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

use Mike42\Escpos\EscposImage;

class PrintController extends Controller
{
    public function printOrder (Order $order)
    {                
        $orderMenus = $order->order_menus()
                     ->join('menus', 'menus.id', 'order_menus.menu_id')
                     ->join('menu_groups', 'menu_groups.id', 'menus.menu_group_id')                    
                     ->selectRaw("order_menus.id as id, menu_id, SUM(quantity) as quantity, menus.menu_group_id as menu_group_id, order_menus.price as price, is_foc, order_menus.status as status, order_menus.created_at as created_at, menu_groups.name as menu_group_name")                
                     ->groupBy('menu_id', 'is_foc')                     
                     ->with('menu', 'menu.menu_group') 
                     ->get();

        $printer_connector = Setting::getPrinterConnector();        

        $connector = new FilePrintConnector($printer_connector);
        $printer = new Printer($connector);

        foreach ($orderMenus->groupBy('menu_group_name') as $key => $orderMenusGroupedBy)
        {
            //print each page            
            $width = 570;
            $height = 80 * count($orderMenusGroupedBy);
            $im = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($im, 255, 255, 255);
            $grey = imagecolorallocate($im, 128, 128, 128);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefilledrectangle($im, 0, 0, $width, $height, $white);


            $font = realpath('fonts/Padauk-Regular.ttf');
                        

            $text = "";

            $menu_group_name = $key ."\n ======= \n \n ";
   
            imagettftext($im, 20, 0, 10, 30, $black, $font, $menu_group_name);            

            $initY = 80;


            foreach ($orderMenusGroupedBy as $om)
            {
                
                $menu_name = $om->menu->name;
                $qty = $om->quantity;
                $price = $om->price;
                $subtotal = $qty*$price;

                $text .= $menu_name;
                $text .= " x ";
                $text .= $qty;
                $text .= " = ";
                $text .= $subtotal. " ကျပ်";

                $text .= "\n \n";


                imagettftext($im, 20, 0, 10, $initY, $black, $font, $text);
            }
            
            imagepng($im, "print.png");            

            
            $img = EscposImage::load("print.png");
            $printer -> bitImage($img);
            $printer -> cut();
        	
        }
        
        	
        $printer -> close();    
        
        
        return view('admin.print.preview');
    }

}
