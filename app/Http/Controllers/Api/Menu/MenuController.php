<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\MenuGroup;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    function getMenusByMenuGroup () {
        $menugroups = MenuGroup::with('menus')->get();
        return $menugroups->toJson();
    }
}
