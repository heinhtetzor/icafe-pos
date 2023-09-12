<?php

use App\Expense;
use App\ExpenseItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecalculateExpensesOrderForLargeDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $expenses_with_overflow_number = Expense::where('total', '>=', 999999.99)->orWhere('total', '=', '0.00')
        ->get();

        foreach ($expenses_with_overflow_number as $expense) {
            $expense_items = $expense->expense_items;
            if (!is_null($expense_items)) {
                $expense_total = $expense_items->sum(function($t) {
                    return $t->quantity * $t->cost;
                });
                $expense->total = $expense_total;
                $expense->save();
            }

            $expense_stock_menus = $expense->expense_stock_menus;
            if (!is_null($expense_stock_menus)) {
                $expense_total = $expense_stock_menus->sum(function($t) {
                    return $t->quantity * $t->cost;
                });
                $expense->total = $expense_total;
                $expense->save();
            }
        
            
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
