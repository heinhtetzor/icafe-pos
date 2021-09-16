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
        "type",
        "datetime",
        "remarks",
        "user_id"
    ];
 
    protected $casts = [
        "datetime" => 'datetime'
    ];

    protected static function boot ()
    {
        parent::boot();
        static::saved (function ($model) {
            
        });
    }

    const DRAFT = 0;
    const SUBMITTED = 1;

    const TYPE_NON_STOCK = 0;
    const TYPE_STOCK = 1;

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

    public static function getSummaryByExpenseStock ($id)
    {
        //for summary panel
        $expenseMenuGroups=DB::table('expense_stock_menus')
        ->join('stock_menus', 'expense_stock_menus.stock_menu_id', '=', 'stock_menus.id')
        ->leftjoin('menus', 'stock_menus.menu_id', '=', 'menus.id')
        ->leftjoin('menu_groups', 'menus.menu_group_id', '=', 'menu_groups.id')
        ->join('expenses', 'expenses.id', '=', 'expense_stock_menus.expense_id')                      
        ->selectRaw('menu_groups.id as id, menu_groups.name as name, SUM(expense_stock_menus.quantity) as quantity, SUM(expense_stock_menus.quantity*expense_stock_menus.cost) as total')
        ->where('expenses.id', '=', $id)                              
        ->groupBy('menus.menu_group_id')
        ->get();  
        
        return $expenseMenuGroups;   
    }

    public function addExpenseItem ($data)
    {
        $menuGroupId = $data["menu_group_id"];
        if  ($data["is_general_item"] == 1) {
            $menuGroupId = null;
        }

        $is_old = ExpenseItem::where('item_id', $data["item_id"])
                                 ->where('expense_id', $data["expense_id"])
                                 ->where('menu_group_id', $menuGroupId)
                                 ->where('cost', $data["cost"])
                                 ->where('is_general_item', $data["is_general_item"])
                                 ->where('unit', $data["unit"])
                                 ->first();
            

        $expense_item = null;                                 
        if (is_null($is_old)) { //new
            $expense_item = ExpenseItem::create([
                "expense_id" => $data["expense_id"],
                "quantity" => $data["quantity"],
                "cost" => $data["cost"],
                "menu_group_id" => $menuGroupId,
                "item_id" => $data["item_id"],
                "is_general_item" => $data["is_general_item"],
                "unit" => $data["unit"]
            ]);
        }
        if (!is_null($is_old)) { //old
            $is_old->quantity = $is_old->quantity + (int) $data["quantity"];
            $is_old->save();
        }

        return $expense_item ?? $is_old;

    }

    public function addExpenseStockMenu ($data) 
    {        
        $is_old = ExpenseStockMenu::where('stock_menu_id', $data["item_id"])
        ->where('expense_id', $data["expense_id"])        
        ->where('cost', $data["cost"])        
        ->where('unit', $data["unit"])
        ->first();

        $stock_menu = StockMenu::findOrFail($data["item_id"]);        
        $expense_stock_menu = null; 

        if (is_null($is_old)) { //new
            $expense_stock_menu = ExpenseStockMenu::create([
                "expense_id" => $data["expense_id"],
                "quantity" => $data["quantity"],
                "cost" => $data["cost"],                
                "stock_menu_id" => $stock_menu->id,                
                "unit" => $data["unit"]
            ]);            
        }

        if (!is_null($is_old)) { //old
            $is_old->quantity = $is_old->quantity + (int) $data["quantity"];
            $is_old->save();
        }        

        return $expense_stock_menu ?? $is_old;
    }

    public function expense_items ()
    {
        return $this->hasMany('App\ExpenseItem');
    }

    public function expense_stock_menus ()
    {
        return $this->hasMany('App\ExpenseStockMenu');
    }

    public function user ()
    {
        return $this->belongsTo('App\AdminAccount');
    }
}
