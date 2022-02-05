<?php

namespace App\Http\Controllers\Api\Table;

use App\Http\Controllers\Controller;
use App\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function tables () {
        $tables = Table::orderBy('name')->get();
        return $tables->toJson();
    }
}
