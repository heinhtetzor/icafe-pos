<?php

namespace App\Http\Controllers\Api\MenuGroup;

use App\Http\Controllers\Controller;
use App\MenuGroup;
use Illuminate\Http\Request;

class MenuGroupController extends Controller
{
    public function index()
    {
        $menu_groups = MenuGroup::orderBy('name')->get();
        return response()->json([
            "data" => $menu_groups,
        ]);
    }
}
