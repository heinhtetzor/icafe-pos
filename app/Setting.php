<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        "key", "value", "options"
    ];

    public static function getPasscode ()
    {
        return self::where('key', 'passcode')->first()->value;
    }

    public static function getPrinterConnector ()
    {
    	return self::where('key', 'printer_connector')->first()->value;
    }

    public static function getShopInfo ()
    {
        return self::where('key', 'shop_name')->orWhere('key', 'shop_line_1')->orWhere('key', 'shop_line_2')->get();
    }

}
