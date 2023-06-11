<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        "key", "value", "options", "store_id"
    ];

    public static function getPasscode ($store_id)
    {
        return self::where('store_id', $store_id)->where('key', 'passcode')->first()->value;
    }

    public static function getPrinterConnector ($store_id)
    {
    	return self::where('store_id', $store_id)->where('key', 'printer_connector')->first()->value ;
    }

    public static function getBillFooterText ($store_id)
    {
        return self::where('store_id', $store_id)->where('key', 'bill_footer_text')->first()->value ?? null;
    }

    public static function getShopInfo ($store_id)
    {
        return self::where('store_id', $store_id)->where('key', 'shop_name')->orWhere('key', 'shop_line_1')->orWhere('key', 'shop_line_2')->get();
    }

}
