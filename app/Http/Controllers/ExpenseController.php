<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Item;
use App\StockMenu;
use App\MenuGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ExpenseController extends Controller
{
    public function index (Request $request)
    {        
        if ($request->has('invoiceNo')) {
            $expense = Expense::where('invoice_no', $request->invoiceNo)->first();  
            if (is_null($expense)) {
                return redirect()->back()->with('error', 'မရှိပါ');
            }
            return $this->show($expense->id);
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
        $expenses=Expense::orderBy('datetime', 'DESC')
                ->whereBetween('datetime', [$fromTime, $toTime])
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
        $expenses = Expense::orderby('datetime', 'DESC')->simplePaginate(10);
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
        if ($expense->type == Expense::TYPE_NON_STOCK) {
            $items = Item::orderby('name')->get();
        }
        if ($expense->type == Expense::TYPE_STOCK) {
            $items = StockMenu::whereHas('menu', function ($q) {
                $q->orderBy('name');
            })->get();   
        }
        $menu_groups = MenuGroup::orderBy('name')->get();
        return view('admin.expenses.create', [
            "expense" => $expense,
            "items" => $items,
            "menu_groups" => $menu_groups
        ]);
    }

    public function store (Request $request)
    {
        $expense = Expense::create([
            "invoice_no" => Expense::generateInvoiceNumber(),
            "datetime" => $request->datetime,
            "total" => 0,
            "remarks" => $request->remarks,
            "type" => (int) $request->type,
            "status" => Expense::DRAFT,            
            "user_id" => Auth()->guard('admin_account')->user()->id
        ]);
        return $this->edit($expense->id);
    }

    public function show ($id)
    {
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
            "expenseItemMenuGroups" => $expenseItemMenuGroups
        ]);
    }

    public function destroy (Request $request, $id)
    {        
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
}
