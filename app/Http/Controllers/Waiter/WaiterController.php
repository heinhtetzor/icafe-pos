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
        $menuGroups = MenuGroup::with('menus')->get();
        return view('client.waiter.menus', [
            'menuGroups' => $menuGroups
        ]);
    }
}
