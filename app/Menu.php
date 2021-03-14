<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name', 'price', 'image', 'status', 'created_at', 'updated_at', 'menu_group_id'
    ];
    public function menu_group () {
        return $this->belongsTo('App\MenuGroup');
    }
    public static function getActiveMenus() {
        return Menu::where('status', 1)->get();
    }
}
