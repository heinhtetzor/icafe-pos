<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableGroup extends Model
{
    protected $fillable = [
        "name",
        "status"
    ];

    public function tables() 
    {
        return $this->hasMany('App\Table');
    }
}
