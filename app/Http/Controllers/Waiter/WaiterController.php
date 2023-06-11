<?php

namespace App\Http\Controllers\Waiter;

use App\Http\Controllers\Controller;
use App\MenuGroup;
use App\Table;
use Illuminate\Http\Request;

class WaiterController extends Controller
{
    function tables () {
        $TABLE = new Table();
        $tables = $TABLE->getTablesAsc();
        return view('client.waiter.tables', [
            'tables' => $tables
        ]);
    }
    function menus ($tableId) {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $menuGroups = MenuGroup::where('store_id', $store_id)
        ->with('menus')
        ->get();
        return view('client.waiter.menus', [
            'menuGroups' => $menuGroups
        ]);
    }
}
