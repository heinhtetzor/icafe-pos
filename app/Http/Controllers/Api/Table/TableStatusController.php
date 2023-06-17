<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\TableGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableStatusController extends Controller
{
    public function index (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id ?? Auth()->guard('waiter')->user()->store_id;
        $groups = TableGroup::where('store_id', $store_id)
        ->where('status', 1)
        ->with('tables')
        ->whereHas('tables', function ($q) {
            $q->where('status', 1);
        })
        ->with('tables.table_status')->get();
        return response()->json($groups);
    }

    public function show (Request $request)
    {

    }
}