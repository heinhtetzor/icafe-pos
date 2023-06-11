<?php

namespace App\Http\Controllers\Api\MenuGroup;

use App\Http\Controllers\Controller;
use App\MenuGroup;
use Illuminate\Http\Request;

class MenuGroupController extends Controller
{
    public function index()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $menu_groups = MenuGroup::where('store_id', $store_id)->orderBy('name')->get();
        return response()->json([
            "data" => $menu_groups,
        ]);
    }
}
