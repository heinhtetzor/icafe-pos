<?php

namespace App\Http\Controllers\Api\Expense;

use App\Expense;
use App\ExpenseItem;
use App\Http\Controllers\Controller;
use App\Item;
use App\MenuGroup;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function getExpenseItems ($id)
    {
        $expense_items = ExpenseItem::where('expense_id', $id)
                        ->with('menu_group', 'expense', 'item')
                        ->get();
        return response()->json([
            'expense_items' => $expense_items
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

    public function getItem ($itemId)
    {
        $item = Item::findorfail($itemId);
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
            // TODO: group by menu_group_id, item_id, unit
            $menuGroupId = $request->menu_group_id;
            if ($request->is_general_item == 1) {
                $menuGroupId = null;
            }
            if ($request->is_general_item == 0 && empty($request->menu_group_id)) {
                throw new Exception("Menu group is required");
            }
            
            $is_old = ExpenseItem::where('item_id', $request->item_id)
                                 ->where('expense_id', $request->expense_id)
                                 ->where('menu_group_id', $menuGroupId)
                                 ->where('cost', $request->cost)
                                 ->where('is_general_item', $request->is_general_item)
                                 ->where('unit', $request->unit)
                                 ->first();
            

            $expense_item = null;                                 
            if (is_null($is_old)) { //new
                $expense_item = ExpenseItem::create([
                    "expense_id" => $request->expense_id,
                    "quantity" => $request->quantity,
                    "cost" => $request->cost,
                    "menu_group_id" => $menuGroupId,
                    "item_id" => $request->item_id,
                    "is_general_item" => $request->is_general_item,
                    "unit" => $request->unit
                ]);
            }
            if (!is_null($is_old)) { //old
                $is_old->quantity = $is_old->quantity + (int) $request->quantity;
                $is_old->save();
            }
            return response()->json([
                "expense_item" => $expense_item ?? $is_old
            ]);

        }
        catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteExpenseItem (Request $request)
    {
        try {
            $expenseItem = ExpenseItem::findorfail($request->id);
            $expenseItem->quantity = $expenseItem->quantity - 1;
            if ($expenseItem->quantity == 0) {
                $expenseItem->delete();
            } 
            else {
                $expenseItem->save();
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
