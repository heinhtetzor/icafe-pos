<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function tables () {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $tables = Table::where('store_id', $store_id)->orderBy('name')->get();
        return $tables->toJson();
    }
}
