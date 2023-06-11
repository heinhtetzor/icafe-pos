<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name', 'code', 'price', 'image', 'status', 'created_at', 'updated_at', 'menu_group_id', 'store_id'
    ];
    public function menu_group () {
        return $this->belongsTo('App\MenuGroup');
    }
    public function order_menu()
    {
        return $this->hasMany('App\OrderMenu');
    }
    public function stock_menu ()
    {
        return $this->hasOne('App\StockMenu')->where('status', StockMenu::STATUS_ACTIVE);
    }
    public static function getActiveMenus($store_id) {
        return Menu::where('status', 1)
        ->where('store_id', $store_id)
        ->with('stock_menu', 'menu_group')->get();
    }
    
    //dangerous
    //need to optimise
    public static function getActiveMenusOrderByPopularity() 
    {
        return Menu::with('order_menu')
                ->get()
                ->sortByDesc(function ($q) {
                    return $q->order_menu->sum('quantity');
                });
    }
    public function isStockMenu () 
    {
        if (!$this->stock_menu()->exists()) {
            return false;
        }
        return $this->stock_menu->status == StockMenu::STATUS_ACTIVE ? true : false;
    }
}
