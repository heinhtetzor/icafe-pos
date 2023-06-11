<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\TableGroup;
use Illuminate\Http\Request;

class TableStatusController extends Controller
{
    public function index (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id ?? Auth()->guard('waiter')->user()->store_id;
        $groups = TableGroup::where('store_id', $store_id)->with('tables', 'tables.table_status')->get();
        return response()->json($groups);
    }

    public function show (Request $request)
    {

    }
}
