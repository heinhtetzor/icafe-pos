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
use Carbon\Carbon;
use SteveNay\MyanFont\MyanFont;
use File;

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
        $shop_infos = Setting::getShopInfo()->pluck('value');        

        $connector = new FilePrintConnector($printer_connector);
        $printer = new Printer($connector);


        foreach ($orderMenus->groupBy('menu_group_name') as $key => $orderMenusGroupedBy)
        {            
            //print each page            
            $width = 570;            
            $height = 1000;

            if (count($orderMenusGroupedBy) > 14)
            {
                $height = 2000;
            }
            // dd($height);
            $im = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($im, 255, 255, 255);
            $grey = imagecolorallocate($im, 128, 128, 128);
            $black = imagecolorallocate($im, 0, 0, 0);


            $font = realpath('fonts/zawgyi.ttf');

            $Y = 30;


            imagefilledrectangle($im, 0, 0, $width, $height, $white);
            $header_font_size = 14;
            
            foreach ($shop_infos as $shop_info)
            {
                $box = imagettfbbox($header_font_size, 0, $font, $shop_info);
                
                $text_width = abs($box[2]) - abs($box[0]);                

                $image_width = imagesx($im);


                $X = ($image_width - $text_width) / 2;

                
                imagettftext($im, 14, 0, $X, $Y, $black, $font, MyanFont::uni2zg($shop_info. "\n"));
                $Y += 30;                            
            }

            $Y += 10;

            $invoice_no = "ဘောင်ချာနံပါတ် - ".$order->invoice_no;

            imagettftext($im, 14, 0, 10, $Y, $black, $font, MyanFont::uni2zg($invoice_no));            

            $menu_group_name = $key; 
            
            imagettftext($im, 14, 0, $width - 200, $Y, $black, $font, MyanFont::uni2zg($menu_group_name));
            
            $Y += 30;

            imagettftext($im, 14, 0, 10, $Y, $black, $font, "..............................................................................................................................");

            $Y += 50;            
               

            $grandtotal = 0;

            foreach ($orderMenusGroupedBy as $om)
            {
                $text = "";
                
                $menu_name = $om->menu->name;
                $qty = $om->quantity;
                $price = $om->price;
                $subtotal = $qty*$price;

                $grandtotal += $subtotal;

                $text .= $menu_name;

                $text .= " x ";
                $text .= $qty;     

                $subtotal_text = $subtotal. " Ks";

                $text .= "\n \n";

                imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg($text));

                imagettftext($im, 17, 0, $width - 120, $Y, $black, $font, MyanFont::uni2zg($subtotal_text));
                $Y += 40;
            }
            imagettftext($im, 17, 0, 10, $Y, $black, $font, "..............................................................................................................................");

            $Y += 40;

            imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg("စုစုပေါင်း"));
            imagettftext($im, 17, 0, $width - 120, $Y, $black, $font, MyanFont::uni2zg($grandtotal. " Ks"));

            $Y += 40;
            $created_at = "စမှတ်ချိန်  - ". Carbon::parse($order->created_at)->format('h:i A d-M-Y') ."\n";
            imagettftext($im, 14, 0, 10, $Y, $black, $font, MyanFont::uni2zg($created_at)); 
            
            $Y += 40;
            $printed_at = "စာရွက်ထုတ်ချိန်- ". Carbon::now()->format('h:i A d-M-Y') . "\n";
            imagettftext($im, 14, 0, 10, $Y, $black, $font, MyanFont::uni2zg($printed_at)); 
            
            imagepng($im, "print-order.png");            

            
            $img = EscposImage::load("print-order.png");
            $printer -> bitImage($img);
            $printer -> cut();

        	
        }
        File::delete(public_path('print-order.png'));  
        
        	
        $printer -> close();    
        
        
        return redirect()->back();
    }

}
