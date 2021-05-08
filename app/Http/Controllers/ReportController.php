<?php

namespace App\Http\Controllers;

use App\ExpenseItem;
use App\Item;
use Illuminate\Http\Request;
use App\Menu;
use App\OrderMenu;
use App\MenuGroup;
use App\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }    

    public function items (Request $request)
    {
        $fromTime = "";
        $toTime = "";
        if ($request->has('date')) {
            $from=explode(" - ", $request->date)[0];
            $to=explode(" - ", $request->date)[1];
            $fromTime=Carbon::parse($from)->startOfDay();
            $toTime=Carbon::parse($to)->endOfDay();            
        }
        $mgs = [];
        $ms = []; 

        if ($request->menuGroup) {
            $mgs = $request->menuGroup;            
        }
        if ($request->item) {
            $is = $request->item;
        }

        $all_items = Item::with('menu_group')->get();
        $all_menu_groups = MenuGroup::all();

        
        if ($request->has('menuGroup')) {
            //search is_general_item in mgs array
            $is_general_item_requested = array_search("is_general_item", $mgs);
            
            $results = ExpenseItem::whereIn('menu_group_id', $mgs);
            if ($is_general_item_requested) {
                $results->orWhere('is_general_item', 1);
            }            
            $results = $results->whereHas('expense', function ($t) use ($fromTime, $toTime) {
                $t->whereBetween('datetime', [$fromTime, $toTime]);
            })
            ->selectRaw('*, SUM(quantity) as total');
            if ($request->group_by_expense)
            {
                $results->groupby('expense_id', 'is_general_item', 'item_id', 'cost', 'unit', 'menu_group_id');
            }
            else 
            {
                $results->groupby('is_general_item', 'item_id', 'cost', 'unit', 'menu_group_id');
            }
            // ->groupby('expense_id')
            $results = $results
            ->with('expense', 'item', 'menu_group')            
            ->orderby('item_id')
            ->orderby('total', 'desc')
            ->get();
            
            $total = $results->sum(function($t) {
                return $t->cost * $t->total;
            });
            $filtered_menu_groups = MenuGroup::whereIn('id', $mgs)->get();
            return view('admin.reports.items', [
                "items" => $all_items,
                "menuGroups" => $all_menu_groups,
                "results" => $results,
                "fromTime" => $fromTime,
                "toTime" => $toTime,
                "filtered_menu_groups" => $filtered_menu_groups,
                "filtered_items" => [],
                "is_general_item_requested" => $is_general_item_requested,
                "is_group_by_expense" => $request->group_by_expense,
                "total" => $total
            ]);
            
        }
        
        if ($request->has('item')) {
            $results = ExpenseItem::whereIn('item_id', $is)
            ->whereHas('expense', function ($q) use ($fromTime,$toTime) {
                $q->whereBetween('datetime', [$fromTime, $toTime]);
            })
            ->selectRaw('*, SUM(quantity) as total');
            
            if ($request->group_by_expense)
            {
                $results->groupby('expense_id', 'is_general_item', 'item_id', 'cost', 'unit', 'menu_group_id');
            }
            else 
            {
                $results->groupby('is_general_item', 'item_id', 'cost', 'unit', 'menu_group_id');
            }
            $results = $results->groupby('is_general_item', 'item_id', 'cost', 'unit', 'menu_group_id')
            ->with('item', 'expense', 'menu_group')
            ->orderby('item_id')
            ->orderby('total', 'desc')
            ->get();                
            
            $filtered_items = Item::whereIn('id', $is)->get();
            $total = $results->sum(function($t) {
                return $t->cost * $t->total;
            });
            return view('admin.reports.items', [
                "items" => $all_items,
                "menuGroups" => $all_menu_groups,
                "results" => $results,
                "fromTime" => $fromTime,
                "toTime" => $toTime,
                "filtered_items" => $filtered_items,
                "filtered_menu_groups" => [],
                "total" => $total,
                "is_group_by_expense" => $request->group_by_expense
            ]);
        }
        return view('admin.reports.items', [
            "items" => $all_items,
            "menuGroups" => $all_menu_groups,
            "results" => []
        ]);
    }

    public function menus(Request $request)
    {        
        $fromTime = "";
        $toTime = "";
        if ($request->has('date')) {
            $from=explode(" - ", $request->date)[0];
            $to=explode(" - ", $request->date)[1];
            $fromTime=Carbon::parse($from)->startOfDay();
            $toTime=Carbon::parse($to)->endOfDay();            
        }
        $mgs = [];
        $ms = [];
        if ($request->menuGroup) {
            $mgs = $request->menuGroup;
        }
        if ($request->menu) {
            $ms = $request->menu;
        }
        
        $all_menus = Menu::with('menu_group')->get();
        $all_menu_groups = MenuGroup::all();

        if ($request->has('menuGroup')) {
            $results = OrderMenu::whereHas('menu', function($q) use ($mgs) {
                $q->whereIn('menu_group_id', $mgs);
            })
            ->whereHas('order', function ($t) use ($fromTime, $toTime) {
                $t->whereBetween('created_at', [$fromTime, $toTime]);
            })
            ->selectRaw('*, SUM(quantity) as total')
            ->groupby('menu_id', 'price')
            ->with('menu')
            ->orderby('total', 'desc')
            ->get();
            $total = $results->sum(function($t) {
                return $t->price * $t->total;
            });
            $filtered_menu_groups = MenuGroup::whereIn('id', $mgs)->get();
            return view('admin.reports.menus', [
                "menus" => $all_menus,
                "menuGroups" => $all_menu_groups,
                "results" => $results,
                "fromTime" => $fromTime,
                "toTime" => $toTime,
                "filtered_menu_groups" => $filtered_menu_groups,
                "filtered_menus" => [],
                "total" => $total
            ]);
            
        }
        
        if ($request->has('menu')) {
            $results = OrderMenu::whereIn('menu_id', $ms)
            ->whereHas('order', function ($q) use ($fromTime,$toTime) {
                $q->whereBetween('created_at', [$fromTime, $toTime]);
            })
            ->selectRaw('*, SUM(quantity) as total')            
            ->groupby('menu_id', 'price')
            ->with('menu')
            ->orderby('total', 'desc')
            ->get();                
            $filtered_menus = Menu::whereIn('id', $ms)->get();
            $total = $results->sum(function($t) {
                return $t->price * $t->total;
            });
            return view('admin.reports.menus', [
                "menus" => $all_menus,
                "menuGroups" => $all_menu_groups,
                "results" => $results,
                "fromTime" => $fromTime,
                "toTime" => $toTime,
                "filtered_menus" => $filtered_menus,
                "filtered_menu_groups" => [],
                "total" => $total
            ]);
        }
        return view('admin.reports.menus', [
            "menus" => $all_menus,
            "menuGroups" => $all_menu_groups,
            "results" => []
        ]);
        
    }

    public function profitLossIndex (Request $request)
    {
        return view('admin.reports.profit-loss');
    }
}
