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
            $expense = Expense::findorfail($expenseId);
            if ($expense->expense_items->count() <= 0) {
                throw new Exception("ပစ္စည်းအမျိုးအမည်ထည့်သွင်းပါ");
            }
            $expense->update([
                "status" => Expense::SUBMITTED
            ]);
            return response()->json([
                "isOk" => TRUE,
                "message" => "Confirmed expense"
            ]);                 
        }
        catch (Exception $e) {
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
            
            return response()->json([
                "expense_item" => $item
            ]);

        }
        catch (Exception $e) {
            throw $e;
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteExpenseItem (Request $request)
    {
        try {
            $item = null;
            if ($request->type == Expense::TYPE_NON_STOCK) {
                $item = ExpenseItem::findOrFail($request->id);

            }
            else if ($request->type == Expense::TYPE_STOCK) {
                $item = ExpenseStockMenu::findOrFail($request->id);
            }

            if (is_null ($item)) {
                throw new Exception("Item cant be found");
            }

            $item->quantity = $item->quantity - 1;
            if ($item->quantity == 0) {
                $item->delete();
            } 
            else {
                $item->save();
            }
            return response()->json([
                "message" => "Deleted"
            ]);
        }
        catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
