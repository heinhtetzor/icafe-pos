<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseStockMenu extends Model
{
    protected $fillable = ["expense_id", "stock_menu_id", "cost", "quantity", "unit"];

    protected static function boot ()
    {
        parent::boot();
        static::saved (function ($model) {
            $total = 0;            
            foreach ($model->expense->expense_stock_menus as $expense_item) {
                $total += $expense_item->cost * $expense_item->quantity;
            }
            $model->expense->total = $total;
            $model->expense->save();
        });

        static::deleted (function ($model) {
            $total = 0;            
            foreach ($model->expense->expense_stock_menus as $expense_item) {
                $total += $expense_item->cost * $expense_item->quantity;
            }            
            $model->expense->total = $total;
            $model->expense->save();
        });
    }

    public function expense ()
    {
        return $this->belongsTo(Expense::class);
    }

    public function stockMenu ()
    {
        return $this->belongsTo(StockMenu::class);
    }
}
