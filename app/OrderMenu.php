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
