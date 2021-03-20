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
    public function order_menu()
    {
        return $this->hasMany('App\OrderMenu');
    }
    public static function getActiveMenus() {
        return Menu::where('status', 1)->get();
    }
    public static function getActiveMenusOrderByPopularity() 
    {
        return Menu::with('order_menu')
                ->get()
                ->sortByDesc(function ($q) {
                    return $q->order_menu->sum('quantity');
                });
    }
}
