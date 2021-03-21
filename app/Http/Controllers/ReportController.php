<?php

namespace App\Http\Controllers;

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
            ->selectRaw('*, SUM(quantity) as total')
            ->whereBetween('created_at', [$fromTime, $toTime])
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
            ->selectRaw('*, SUM(quantity) as total')
            ->whereBetween('created_at', [$fromTime, $toTime])
            ->groupby('menu_id', 'price')
            ->with('menu')
            ->orderby('total', 'desc')
            ->get();                
            $filtered_menus = Menu::whereIn('id', $ms)->get();
            // dd($filtered_menus);
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
}
