<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockMenuEntry extends Model
{
    protected $fillable = ["cost", "in", "out", "balance", "expense_stock_menu_id", "order_menu_id"];

    public function stockMenu()
    {
        return $this->belongsTo(StockMenu::class);
    }

    public function expenseStockMenu ()
    {
        return $this->belongsTo(ExpenseStockMenu::class);
    }

    public function orderMenu ()
    {
        return $this->belongsTo(OrderMenu::class);
    }

    public function reference ()
    {
        if (!is_null ($this->expense_stock_menu_id)) {
            return $this->expenseStockMenu();
        }
        
        if (!is_null ($this->order_menu_id)) {            
            return $this->orderMenu();
        }
    }
}
