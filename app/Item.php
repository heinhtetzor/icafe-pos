<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        "name", "cost", "is_general_item", "menu_group_id", "store_id"
    ];

    public function menu_group ()
    {
        return $this->belongsTo('App\MenuGroup', 'menu_group_id');
    }

    public function expense_items ()
    {
        return $this->hasMany('App\ExpenseItem');
    }
}

