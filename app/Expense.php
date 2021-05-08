<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expense extends Model
{
    protected $fillable = [
        "invoice_no",
        "total",
        "status",
        "datetime",
        "remarks",
        "user_id"
    ];
 
    protected $casts = [
        "datetime" => 'datetime'
    ];

    const DRAFT = 0;
    const SUBMITTED = 1;

    public static function generateInvoiceNumber() {
        //get today datevap
        $today = Carbon::today()->today();
        $today_string = $today->format('Y-m-d');
        $latest_order = Expense::whereDate('datetime', $today)->orderby('datetime', 'DESC')->first();        

        if (!empty($latest_order)) {
            // dd($today_string, $latest_order->invoice_no);
            $arr = explode("_", $latest_order->invoice_no);
            $number = intval($arr[1]) + 1;
            $invoice_no = $today_string ."_". $number;
            return $invoice_no;
        }
        else {
            $invoice_no = $today_string ."_". 1;            
            return $invoice_no;
        }        
    }

    public static function getSummaryByExpense ($id)
    {
        //for summary panel
        $expenseMenuGroups=DB::table('expense_items')
        ->join('items', 'expense_items.item_id', '=', 'items.id')
        ->leftjoin('menu_groups', 'expense_items.menu_group_id', '=', 'menu_groups.id')
        ->join('expenses', 'expenses.id', '=', 'expense_items.expense_id')                      
        ->selectRaw('expense_items.is_general_item, menu_groups.id as id, menu_groups.name as name, SUM(expense_items.quantity) as quantity, SUM(expense_items.quantity*expense_items.cost) as total')
        ->where('expenses.id', '=', $id)                              
        ->groupBy('expense_items.menu_group_id')
        ->get();  
        
        return $expenseMenuGroups;   
    }

    public function expense_items ()
    {
        return $this->hasMany('App\ExpenseItem');
    }

    public function user ()
    {
        return $this->belongsTo('App\AdminAccount');
    }
}
