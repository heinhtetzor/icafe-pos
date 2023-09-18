<?php

namespace App\Http\Controllers\Api\Expense;

use App\Expense;
use App\ExpenseItem;
use App\ExpenseStockMenu;
use App\Http\Controllers\Controller;
use App\Item;
use App\MenuGroup;
use App\StockMenu;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function getExpenseItems ($id)
    {
        $items = [];

        $expense = Expense::findOrFail($id);

        if ($expense->type == Expense::TYPE_STOCK) {
            $items = ExpenseStockMenu::where('expense_id', $id)
                        ->with('expense', 'stockMenu', 'stockMenu.menu')
                        ->get();
        }

        if ($expense->type == Expense::TYPE_NON_STOCK) {
            $items = ExpenseItem::where('expense_id', $id)
                            ->with('menu_group', 'expense', 'item')
                            ->get();
        }

        return response()->json([
            'items' => $items
        ]); 
    }

    public function getSummary (Request $request)
    {
        $fromTime = null;
        $toTime = null;

        if($request->date) {            
            $from=explode(" - ", $request->date)[0];
            $to=explode(" - ", $request->date)[1];
            $fromTime=Carbon::parse($from)->startOfDay();
            $toTime=Carbon::parse($to)->endOfDay();
        }
        else {
            $fromTime=now()->startOfDay();
            $toTime=now()->endOfDay();        
        }       
        $store_id = Auth()->guard('admin_account')->user()->store_id;


        $expenseItemMenuGroups=DB::table('expenses')
        ->join('expense_items', 'expenses.id', '=', 'expense_items.expense_id')
        ->join('items', 'expense_items.item_id', '=', 'items.id') 
        ->leftjoin('menu_groups as mg1', 'items.menu_group_id', '=', 'mg1.id')
        ->selectRaw('expense_items.is_general_item, mg1.id as id, mg1.name as name, SUM(expense_items.quantity) as quantity, ROUND(SUM(expense_items.quantity*expense_items.cost), 2) as total')
        ->where('expenses.status', '=', '1')
        ->where('expenses.store_id', '=', $store_id)                      
        ->whereBetween('expenses.datetime', [$fromTime, $toTime])
        ->groupBy('mg1.id')
        ->get();    
        
        $expenseStockMenuGroups=DB::table('expenses')
        ->join('expense_stock_menus', 'expense_stock_menus.expense_id', '=', 'expenses.id')
        ->join('stock_menus', 'expense_stock_menus.stock_menu_id', '=', 'stock_menus.id')
        ->join('menus', 'menus.id', '=', 'stock_menus.menu_id')
        ->leftjoin('menu_groups as mg1', 'mg1.id', '=', 'menus.menu_group_id')
        ->selectRaw('mg1.id as id, mg1.name as name, SUM(expense_stock_menus.quantity) as quantity, ROUND(SUM(expense_stock_menus.quantity*expense_stock_menus.cost), 2) as total')
        ->where('expenses.status', '=', '1')
        ->where('expenses.store_id', '=', $store_id)                      
        ->whereBetween('expenses.datetime', [$fromTime, $toTime])
        ->groupBy('mg1.id')
        ->get();    

        //merge two array into one
        $final_arr = [];
        $map = [];
        foreach ($expenseItemMenuGroups as $k => $expenseItem) {

            if (array_search($expenseItem->id, $map)) {
                foreach ($final_arr as $final_arr_item) {
                    if ($final_arr_item->id == $expenseItem->id) {
                        $final_arr_item->total += $expenseItem->total;
                    }
                }
            } else {
                array_push($final_arr, $expenseItem);
                array_push($map, $expenseItem->id);
            }
        }
        foreach ($expenseStockMenuGroups as $k => $expenseStockMenu) {
            # code...
            if (array_search($expenseStockMenu->id, $map)) {
                foreach ($final_arr as $final_arr_item) {
                    if ($final_arr_item->id == $expenseStockMenu->id) {
                        $final_arr_item->total += $expenseStockMenu->total;
                    }
                }
            } else {
                array_push($final_arr, $expenseStockMenu);
                array_push($map, $expenseStockMenu->id);
            }
        }

        return response()->json([
            "expenseItemMenuGroups" => $final_arr,
        ]);
    }

    public function confirmExpense ($expenseId)
    {
        try {
            DB::beginTransaction();
            $expense = Expense::findorfail($expenseId);
            if ($expense->expense_items->count() <= 0 && $expense->expense_stock_menus->count() <= 0) {
                throw new Exception("ပစ္စည်းအမျိုးအမည်ထည့်သွင်းပါ");
            }
            $expense->update([
                "status" => Expense::SUBMITTED
            ]);

            if ($expense->type == Expense::TYPE_STOCK) {                             
                foreach ($expense->expense_stock_menus as $expense_stock_menu)
                {
                    $stock_menu = $expense_stock_menu->stockMenu;
                    $old_balance = $stock_menu->balance;
                    $stock_menu->balance = $old_balance + $expense_stock_menu->quantity;
                    $stock_menu->save();
        
                    $stock_menu->stockMenuEntries()->create([
                        "expense_stock_menu_id" => $expense_stock_menu->id,
                        "cost" => $expense_stock_menu->cost,
                        "in" => $expense_stock_menu->quantity,
                        "out" => 0,
                        "balance" => $stock_menu->balance
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                "isOk" => TRUE,
                "message" => "Confirmed expense"
            ]);                 
        }
        catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function getItem (Request $request, $itemId)
    {
        if ($request->type == Expense::TYPE_NON_STOCK) {
            $item = Item::findorfail($itemId);
        }
        if ($request->type == Expense::TYPE_STOCK) {
            $item = StockMenu::findorfail($itemId);
        }
        
        return response()->json([
            "item" => $item,
        ]);
    }

    public function getExpense ($expenseId)
    {
        $expense = Expense::findorfail($expenseId);
        return response()->json([
            "expense" => $expense
        ]);
    }

    public function addExpenseItem (Request $request)
    {        
        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($request->expense_id);
            // TODO: group by menu_group_id, item_id, unit
   
            if ($expense->type == Expense::TYPE_NON_STOCK && $request->is_general_item == 0 && empty($request->menu_group_id)) {
                throw new Exception("Menu group is required");
            }

            $item = null;

            if ($expense->type == Expense::TYPE_NON_STOCK) {
                $item = $expense->addExpenseItem($request->all());
            }

            if ($expense->type == Expense::TYPE_STOCK) {
                $item = $expense->addExpenseStockMenu($request->all());
            }
            

            DB::commit();
            return response()->json([
                "expense_item" => $item
            ]);

        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }


    //before confirming
    public function deleteExpenseItem (Request $request)
    {
        try {
            DB::beginTransaction();

            $item = null;
            $item_name = null;//for logging
            $cancel_quantity = 1;
            if ($request->type == Expense::TYPE_NON_STOCK) {
                $item = ExpenseItem::findOrFail($request->id);
                $item_name = $item->item->name;

            }
            else if ($request->type == Expense::TYPE_STOCK) {
                $item = ExpenseStockMenu::findOrFail($request->id);
                $item_name = $item->stockMenu->menu->name;
            }

            $expense = $item->expense;

            if (is_null ($item)) {
                throw new Exception("Item cant be found");
            }

            if (!is_null ($request->cancelQuantity)) {
                $cancel_quantity = $request->cancelQuantity;
            }

            if ($cancel_quantity < 1) {
                throw new Exception("Cancel quantity cannot be less than 1");
            }

            if ($cancel_quantity > $item->quantity) {
                throw new Exception("Cancel quantity cannot be less than the existing quantity");
            }

            //if expense is submitted and it is stock
            //only confirmed have stock 
            if ($expense->status == Expense::SUBMITTED && $request->type == Expense::TYPE_STOCK) { // reduce stock level item quantity
                $stock_menu = $item->stockMenu;
                $stock_menu->lockForUpdate();
                $stock_menu->balance = $stock_menu->balance - $cancel_quantity;
                $stock_menu->save();
            }

            //reducing item from expense_item
            if ($item->quantity > 0 && $item->quantity < 1) {
                $item->quantity = 0;                
            } else {
                $item->quantity = $item->quantity - $cancel_quantity;
            }
            

            $item->save();
            

            //record stock entry transaction
            if ($request->type == Expense::TYPE_STOCK) {
                $stock_menu->stockMenuEntries()->create([
                    "expense_stock_menu_id" => $item->id,
                    "cost" => $item->cost,
                    "in" => 0,
                    "out" => $cancel_quantity,
                    "balance" => $stock_menu->balance
                ]);
            }

            if ($expense->status == Expense::SUBMITTED && $cancel_quantity > 0) {
                //log deleted row
                $expense->logDeletion([
                    "item_name" => $item_name,
                    "cost" => $item->cost,
                    "quantity" => $cancel_quantity,
                    "deleted_at" => Carbon::now()->format('d-M-Y h:i A')
                ]);
            }

            DB::commit();


            return response()->json([
                "message" => "Deleted"
            ]);
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
