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
        $expenses_with_overflow_number = Expense::where('total', '>=', 999999.99)
        ->get();

        foreach ($expenses_with_overflow_number as $expense) {
            $expense_items = $expense->expense_items;
            if (is_null($expense_items)) {
                continue;
            }
            $expense_total = $expense_items->sum(function($t) {
                return $t->quantity * $t->cost;
            });
        
            $expense->total = $expense_total;
            $expense->save();
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
