<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = [
        "menu_group_id",
        "expense_id",
        "item_id",
        "is_general_item",
        "cost",
        "quantity",
        "unit"
    ];

    protected static function boot ()
    {
        parent::boot();
        static::saved (function ($model) {
            $total = 0;            
            foreach ($model->expense->expense_items as $expense_item) {
                $total += $expense_item->cost * $expense_item->quantity;
            }
            $model->expense->total = $total;
            $model->expense->save();
        });

        static::deleted (function ($model) {
            $total = 0;            
            foreach ($model->expense->expense_items as $expense_item) {
                $total += $expense_item->cost * $expense_item->quantity;
            }            
            $model->expense->total = $total;
            $model->expense->save();
        });
    }

    public function expense ()
    {
        return $this->belongsTo('App\Expense', 'expense_id');
    }

    public function item ()
    {
        return $this->belongsTo('App\Item', 'item_id');
    }

    public function menu_group ()
    {
        return $this->belongsTo('App\MenuGroup', 'menu_group_id');
    }
}
