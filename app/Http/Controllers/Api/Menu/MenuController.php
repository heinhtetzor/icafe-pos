<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Menu;
use App\MenuGroup;
use App\StockMenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index ()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $menus = Menu::getActiveMenus($store_id);
        return response()->json([
            "data" => $menus,
        ]);
    }

    public function getMenusByMenuGroup () {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $menugroups = MenuGroup::where('store_id', $store_id)->with('menus')->get();
        return $menugroups->toJson();
    }

    public function getStockMenus (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $stock_menus = StockMenu::whereHas('menu', function ($q) use ($store_id) {
            $q->where('store_id', $store_id);
        })
        ->with('menu')
        ->get();
        return response()->json([
            "stock_menus" => $stock_menus
        ]);     
    }
}
