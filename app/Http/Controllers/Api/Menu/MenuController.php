<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\MenuGroup;
use App\StockMenu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getMenusByMenuGroup () {
        $menugroups = MenuGroup::with('menus')->get();
        return $menugroups->toJson();
    }

    public function getStockMenus (Request $request)
    {
        $stock_menus = StockMenu::with('menu')->get();
        return response()->json([
            "stock_menus" => $stock_menus
        ]);     
    }
}
