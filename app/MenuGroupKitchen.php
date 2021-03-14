<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MenuGroupKitchen extends Model
{
    protected $fillable=['menu_group_id', 'kitchen_id'];

    public function menu_group()
    {
    	return $this->belongsTo('App\MenuGroup');
    }

    public function kitchen()
    {
    	return $this->belongsTo('App\Kitchen');
    }
}
