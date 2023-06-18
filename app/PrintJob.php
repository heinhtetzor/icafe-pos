<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    protected $fillable = ['store_id', 'type', 'order_id', 'order_menu_id', 'expense_id', 'status'];

    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2; 

    const TYPE_ORDER_BILL = "order_bill";
    const TYPE_ORDER_MENU_TABLE_SLIP = "order_menu_table_slip";
    const TYPE_ORDER_MENU_EXPRESS_SLIP = "order_menu_express_slip";
    const TYPE_EXPENSE = "expense";

    public function store () {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function order () {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderMenu () {
        return $this->belongsTo('App\OrderMenu', 'order_menu_id');
    }

    public function expense () {
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}
