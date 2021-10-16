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

        $expenseItemMenuGroups=DB::table('expense_items')
                      ->join('items', 'expense_items.item_id', '=', 'items.id')
                      ->leftjoin('menu_groups', 'items.menu_group_id', '=', 'menu_groups.id')
                      ->join('expenses', 'expenses.id', '=', 'expense_items.expense_id')                      
                      ->selectRaw('expense_items.is_general_item, menu_groups.id as id, menu_groups.name as name, SUM(expense_items.quantity) as quantity, SUM(expense_items.quantity*expense_items.cost) as total')
                      ->where('expenses.status', '=', '1')                      
                      ->whereBetween('expenses.datetime', [$fromTime, $toTime])
                      ->groupBy('menu_groups.id')
                      ->get();  
        return response()->json([
            "expenseItemMenuGroups" => $expenseItemMenuGroups
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

            if (!is_null ($request->cancel_quantity)) {
                $cancel_quantity = $request->cancelQuantity;
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

            if ($item->quantity == 0) {
                $item->delete();
            } 
            else {
                $item->save();
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
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
