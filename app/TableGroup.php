<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableGroup extends Model
{
    protected $fillable = [
        "name",
        "status",
        "store_id",
    ];

    public function tables() 
    {
        return $this->hasMany('App\Table');
    }
}
