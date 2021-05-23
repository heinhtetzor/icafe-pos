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

}
