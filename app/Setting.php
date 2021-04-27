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
}
