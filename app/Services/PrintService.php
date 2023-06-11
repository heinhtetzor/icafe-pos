<?php
namespace App\Services;
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

use App\Http\Traits\OrderFunctions;
use App\Menu;
use App\Waiter;

class PrintService {
    use OrderFunctions;
    public static function printOrderSummary (Order $order)
    {
        $orderMenus = $order->order_menus()
                     ->join('menus', 'menus.id', 'order_menus.menu_id')
                     ->join('menu_groups', 'menu_groups.id', 'menus.menu_group_id')                    
                     ->selectRaw("order_menus.id as id, menu_id, SUM(quantity) as quantity, menus.menu_group_id as menu_group_id, order_menus.price as price, is_foc, order_menus.status as status, order_menus.created_at as created_at, menu_groups.name as menu_group_name")                
                     ->groupBy('menu_id', 'is_foc')                     
                     ->with('menu', 'menu.menu_group') 
                     ->get();

        $printer_connector = Setting::getPrinterConnector($order->store_id);
        if (! $printer_connector) {
            throw new \Exception("Printer Connector cannot be null");
        }
        $shop_infos = Setting::getShopInfo($order->id)->pluck('value');        

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
        // File::delete(public_path('print-order.png'));  
        
        	
        $printer -> close();    
        
        
        return true;
    }

    public static function printOrderBill (Order $order)
    {
        $order_menus = OrderFunctions::getOrderMenusGrouped($order);

        //connect to printer
        $printer_connector = Setting::getPrinterConnector($order->store_id);
        if (! $printer_connector) {
            throw new \Exception("Printer Connector cannot be null");
        }        
        $shop_infos = Setting::getShopInfo($order->store_id)->pluck('value');       

        $bill_footer_text = Setting::getBillFooterText($order->store_id) ?? "ကျေးဇူးတင်ပါသည်";

        $connector = new FilePrintConnector($printer_connector);
        $printer = new Printer($connector);

        //print each page            
        $width = 570;            

        $fixed_extra_height = 500;
        $height = $fixed_extra_height + (count($order_menus)*60);
        
        $height = 71 * count($order_menus) + $fixed_extra_height;

        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $grey = imagecolorallocate($im, 128, 128, 128);
        $black = imagecolorallocate($im, 0, 0, 0);


        $font = realpath('fonts/zawgyi.ttf');

        $Y = 30;

        imagefilledrectangle($im, 0, 0, $width, $height, $white);
        $header_font_size = 14;   

        foreach ($shop_infos as $i => $shop_info)
        {
            $box = imagettfbbox($header_font_size, 0, $font, $shop_info);
            
            $text_width = abs($box[2]) - abs($box[0]);                

            $image_width = imagesx($im);


            $X = ($image_width - $text_width) / 2;

            //header font for first iteration which is shop name
            imagettftext($im, $i === 0 ? $header_font_size : 14, 0, $X, $Y, $black, $font, MyanFont::uni2zg($shop_info. "\n"));
            $Y += 30;                            
        }

        $Y += 10;

        $invoice_no = "ဘောင်ချာနံပါတ် - ".$order->invoice_no;

        imagettftext($im, 14, 0, 10, $Y, $black, $font, MyanFont::uni2zg($invoice_no));   


        $waiter_name = "Waiter - " .$order->waiter->name ?? "-";

        imagettftext($im, 14, 0, $width - 200, $Y, $black, $font, MyanFont::uni2zg($waiter_name));

        $Y += 30;

        $table_name = $order->table->name ?? "-";

        imagettftext($im, 14, 0, 10, $Y, $black, $font, MyanFont::uni2zg($table_name));   

        $Y += 30;

        imagettftext($im, 14, 0, 10, $Y, $black, $font, "..............................................................................................................................");

        $Y += 50;            

        $grandtotal = 0;

        foreach ($order_menus as $om)
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

        //put in center
        $box = imagettfbbox(10, 0, $font, $bill_footer_text);
            
        $text_width = abs($box[2]) - abs($box[0]);                

        $image_width = imagesx($im);


        $X = ($image_width - $text_width) / 2;

        
        imagettftext($im, 10, 0, $X, $height-50, $black, $font, MyanFont::uni2zg($bill_footer_text));            
        
        imagepng($im, "print-bill.png");            

        
        $img = EscposImage::load("print-bill.png");
        $printer -> bitImage($img);
        $printer -> cut();

        // File::delete(public_path('print-bill.png'));  
        $printer->close();
        return true;
    }

    public static function printOrderSlipExpress (OrderMenu $orderMenu)
    {        
        if ($orderMenu->menu->menu_group->print_slip == 1)
        {
            $printer_connector = Setting::getPrinterConnector($orderMenu->order->store_id);
            if (! $printer_connector) {
                throw new \Exception("Printer Connector cannot be null");
            }                        
            $connector = new FilePrintConnector($printer_connector);
            $printer = new Printer($connector);

            $width = 570;            
            $height = 200;

            $im = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($im, 255, 255, 255);                
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

            if (is_null($orderMenu->waiter_id)) {
                $waiter = "Express";
            }
            if (!is_null($orderMenu->waiter_id)) {
                $waiter = $orderMenu->waiter->name;
            }

            imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg($waiter));

            imagepng($im, "print-slip.png");            

            
            $img = EscposImage::load("print-slip.png");
            $printer -> bitImage($img);
            $printer -> cut();


            // File::delete(public_path('print-slip.png'));
            $printer->close();

            return true;
        }
    }

    //grouped by table, menu group
    public static function printOrderSlipTable (Order $order, $waiterId, $printMenuGroups)
    {        


        foreach ($printMenuGroups as $key => $orderMenus)
        {
            $printer_connector = Setting::getPrinterConnector($order->store_id);
            if (! $printer_connector) {
                throw new \Exception("Printer Connector cannot be null");
            }                        
            $connector = new FilePrintConnector($printer_connector);
            $printer = new Printer($connector);

            $width = 570;            
            $height = count($orderMenus)*60  +140;
            

            $im = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($im, 255, 255, 255);                
            $black = imagecolorallocate($im, 0, 0, 0);


            $font = realpath('fonts/zawgyi.ttf');

            $Y = 30;

            imagefilledrectangle($im, 0, 0, $width, $height, $white);
            $font_size = 20;
            
            imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg(Carbon::parse($order->created_at)->format('h:i A d-M-Y')));

            $Y += 50;  

            $count = 0; //if count zero, dont print

            $waiter_name = Waiter::findorfail($waiterId)->name;
            $count = 0;

            foreach ($orderMenus as $key => $orderMenu)
            {
                $m = Menu::findorfail($orderMenu["menu_id"]);

                $menu_name = $m->name;
                $qty = $orderMenu["qty"];

                imagettftext($im, $font_size, 0, 10, $Y, $black, $font, MyanFont::uni2zg($menu_name));
                imagettftext($im, $font_size, 0, $width - 130, $Y, $black, $font, MyanFont::uni2zg($qty));
                $Y += 50;          
                $count++;                    
            }

            if ($count === 0) 
            {
                $printer->close();
                return true;
            }

            $Y += 30;

            imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg("Table - ".$order->table->name));
            
            $Y += 30;

            imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg("Waiter - " .$waiter_name));


            imagepng($im, "print-slip-table.png");

            $img = EscposImage::load("print-slip-table.png");
            $printer -> bitImage($img);
            $printer -> cut();

            // File::delete(public_path('print-slip-table.png'));
            $printer->close();
        }        


        return true;
    }
    public static function printOrderMenuReport ($lines, $store_id)
    {
        $printer_connector = Setting::getPrinterConnector($store_id);
        if (! $printer_connector) {
            throw new \Exception("Printer Connector cannot be null");
        }                        
        $connector = new FilePrintConnector($printer_connector);
        $printer = new Printer($connector);

        $width = 570;            
        $height = count($lines)*60  +140;

        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);                
        $black = imagecolorallocate($im, 0, 0, 0);


        $font = realpath('fonts/zawgyi.ttf');

        $Y = 30;

        imagefilledrectangle($im, 0, 0, $width, $height, $white);
        $font_size = 20;        

        $Y += 50;  

        $count = 0; //if count zero, dont print

        foreach ($lines as $key => $line)
        {   
            $menu_name = $line["menuName"];
            $price = $line["menuPrice"];
            $qty = $line["menuQuantity"];
            $total = $line["total"];

            $text = $menu_name." ".$qty." x ".$price;

            imagettftext($im, $font_size, 0, 10, $Y, $black, $font, MyanFont::uni2zg($text));
            imagettftext($im, $font_size, 0, $width - 130, $Y, $black, $font, MyanFont::uni2zg($total));
            $Y += 50;          
            $count++;              
        }

         if ($count === 0) 
        {
            $printer->close();
            return true;
        }

        $Y += 30;

        imagepng($im, "print-menu-report.png");

        $img = EscposImage::load("print-menu-report.png");
        $printer -> bitImage($img);
        $printer -> cut();

        // File::delete(public_path('print-slip-table.png'));
        $printer->close();

        return true;
    }
}
