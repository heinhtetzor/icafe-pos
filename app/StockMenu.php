<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockMenu extends Model
{
    protected $fillable = [
        "menu_id", "purchase_cost", "sales_price", "balance", "status"
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public static function createStockMenu (Menu $menu) 
    {        
        $stock_menu = StockMenu::updateOrCreate([
            "menu_id" => $menu->id
        ], [
            "purchase_cost" => 0,
            "sales_price" => 0,
            "status" => self::STATUS_ACTIVE
        ]);
        return $stock_menu;
    }

    public static function disableStockMenu (Menu $menu) 
    {
        $stock_menu = $menu->stock_menu;
        if (!is_null($stock_menu)) {
            $stock_menu->status = self::STATUS_INACTIVE;       
            $stock_menu->save();

            return $stock_menu;            
        }
    }

    public function menu () 
    {
        return $this->belongsTo('App\Menu');
    }

    public function stockMenuEntries ()
    {
        return $this->hasMany(StockMenuEntry::class);
    }
}
