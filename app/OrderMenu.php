<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

//printer stuff
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use SteveNay\MyanFont\MyanFont;
use File;
use Mike42\Escpos\EscposImage;

class OrderMenu extends Model
{
    protected $fillable = ['waiter_id', 'order_id', 'menu_id', 'quantity', 'is_foc', 'status', 'price'];

    // protected static function boot ()
    // {
    //     parent::boot();
    //     static::saved (function ($orderMenu) {
    //         if ($orderMenu->menu->menu_group->print_slip == 1)
    //         {

    //             $printer_connector = Setting::getPrinterConnector();                        
    //             $connector = new FilePrintConnector($printer_connector);
    //             $printer = new Printer($connector);

    //             $width = 570;            
    //             $height = 200;

    //             $im = imagecreatetruecolor($width, $height);
    //             $white = imagecolorallocate($im, 255, 255, 255);                
    //             $black = imagecolorallocate($im, 0, 0, 0);


    //             $font = realpath('fonts/zawgyi.ttf');

    //             $Y = 30;

    //             imagefilledrectangle($im, 0, 0, $width, $height, $white);
    //             $font_size = 20;
                
    //             imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg(Carbon::parse($orderMenu->created_at)->format('h:i A d-M-Y')));

    //             $Y += 50;            

    //             imagettftext($im, $font_size, 0, 10, $Y, $black, $font, MyanFont::uni2zg($orderMenu->menu->name));
                
    //             imagettftext($im, $font_size, 0, $width - 130, $Y, $black, $font, MyanFont::uni2zg($orderMenu->quantity));
    //             $Y += 50;

    //             $waiter = null;                

    //             if (is_null($orderMenu->waiter_id)) {
    //                 $waiter = "Express";
    //             }
    //             if (!is_null($orderMenu->waiter_id)) {
    //                 $waiter = $orderMenu->waiter->name;
    //             }

    //             imagettftext($im, 17, 0, 10, $Y, $black, $font, MyanFont::uni2zg($waiter));

    //             imagepng($im, "print-slip.png");            

                
    //             $img = EscposImage::load("print-slip.png");
    //             $printer -> bitImage($img);
    //             $printer -> cut();


    //             File::delete(public_path('print-slip.png'));
    //             $printer->close();
    //         }
    //     });
    // }
    
    function getStatus () {
        //0 : sent to kitchen
        //1 : serve to customer
        return $this->status;
    }
    
    function order() {
        return $this->belongsTo('App\Order');
    }

    function menu() {
        return $this->belongsTo('App\Menu');
    }

    function waiter() {
        return $this->belongsTo('App\Waiter');
    }
}
