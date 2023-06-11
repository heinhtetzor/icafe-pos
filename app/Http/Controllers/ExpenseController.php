<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Item;
use App\StockMenu;
use App\MenuGroup;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ExpenseController extends Controller
{
    public function index (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;        
        if ($request->has('invoiceNo')) {
            $expense = Expense::where('store_id', $store_id)
            ->where('invoice_no', $request->invoiceNo)->first();  
            if (is_null($expense)) {
                return redirect()->back()->with('error', 'မရှိပါ');
            }
            return $this->show($request, $expense->id);
        }

        $fromTime = null;
        $toTime = null;
        $isToday=FALSE;
        if($request->has('date')) {
            // $from=date($request->date)->startOfDay(); 
            $from=explode(" - ", $request->date)[0];
            $to=explode(" - ", $request->date)[1];
            $fromTime=Carbon::parse($from)->startOfDay();
            $toTime=Carbon::parse($to)->endOfDay();
        }
        else {
            $fromTime=now()->startOfDay();
            $toTime=now()->endOfDay();
            $isToday=TRUE;
        }
        // dd($fromTime, $toTime);
        //get today orders
        $expenses=Expense::where('store_id', $store_id)
                ->whereBetween('datetime', [$fromTime, $toTime])
                ->orderBy('datetime', 'DESC')
                ->simplePaginate(20);
        return view('admin.expenses.index', [
            'expenses' => $expenses,
            'fromTime'=>$fromTime,
            'toTime'=>$toTime,
            'isToday' => $isToday
        ]);
    }

    public function create ()
    {                        
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $expenses = Expense::where('store_id', $store_id)
        ->orderby('datetime', 'DESC')
        ->simplePaginate(10);
        return view('admin.expenses.create', [            
            "invoice_no" => Expense::generateInvoiceNumber(),
            "expenses" => $expenses
        ]);
    }

    public function edit ($id)
    {
        $expense = Expense::findorfail($id);
        if ($expense->status == Expense::SUBMITTED)
        {
            return redirect(route('expenses.show', $expense->id));
        }

        $store_id = Auth()->guard('admin_account')->user()->store_id;
        if ($expense->type == Expense::TYPE_NON_STOCK) {
            $items = Item::where('store_id', $store_id)
                    ->orderby('name')
                    ->get();
        }
        if ($expense->type == Expense::TYPE_STOCK) {
            $items = StockMenu::whereHas('menu', function ($q) use ($store_id) {
                $q->where('store_id', $store_id);
                $q->orderBy('name');
            })
            ->where('status', StockMenu::STATUS_ACTIVE)
            ->get();   
        }
        $menu_groups = MenuGroup::where('store_id', $store_id)
                        ->orderBy('name')
                        ->get();
        return view('admin.expenses.create', [
            "expense" => $expense,
            "items" => $items,
            "menu_groups" => $menu_groups
        ]);
    }

    public function store (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $expense = Expense::create([
            "invoice_no" => Expense::generateInvoiceNumber(),
            "datetime" => $request->datetime,
            "total" => 0,
            "remarks" => $request->remarks,
            "type" => (int) $request->type,
            "status" => Expense::DRAFT,            
            "user_id" => Auth()->guard('admin_account')->user()->id,
            "store_id" => $store_id
        ]);
        return $this->edit($expense->id);
    }

    public function show (Request $request, $id)
    {
        $is_edit_mode = false;
        $from_search_result = false;
        if ($request->edit=="true") {
            $is_edit_mode = true;
        }
        if ($request->from_search_result) {
            $from_search_result = true;
        }

        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $passcode = Setting::getPasscode($store_id);

        $expense = Expense::findorfail($id);

        if ($expense->type == Expense::TYPE_NON_STOCK) {
            $expenseItems = $expense->expense_items()->with('menu_group')->get();
            $expenseItemMenuGroups = Expense::getSummaryByExpense($id);
        }

        if ($expense->type == Expense::TYPE_STOCK) {
            $expenseItems = $expense->expense_stock_menus;
            $expenseItemMenuGroups = Expense::getSummaryByExpenseStock($id);
        }

        return view ('admin.expenses.show', [
            "expense" => $expense,

            "expense_items" => $expenseItems,
            "expenseItemMenuGroups" => $expenseItemMenuGroups,
            'is_edit_mode'=>$is_edit_mode,
            'from_search_result'=>$from_search_result,
            'passcode' => $passcode
        ]);
    }

    public function destroy (Request $request, $id)
    {        
        try {
            $section = explode("/", URL::previous());
            
            $expense = Expense::findorfail($id);
            $expense->delete();

            if ($section[5] == "edit") 
            {
                return redirect(route('expenses.create'));
            }
            else 
            {
                return redirect(route('expenses.index'));
            }
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'ဖျက်လို့မရပါ');
        }
    }
}
