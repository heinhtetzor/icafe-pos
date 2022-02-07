<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\TableGroup;
use Illuminate\Http\Request;

class TableStatusController extends Controller
{
    public function index (Request $request)
    {
        $groups = TableGroup::with('tables', 'tables.table_status')->get();
        return response()->json($groups);
    }

    public function show (Request $request)
    {

    }
}
