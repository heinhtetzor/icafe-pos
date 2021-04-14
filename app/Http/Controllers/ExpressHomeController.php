<?php

namespace App\Http\Controllers;

use App\Menu;
use Illuminate\Http\Request;

class ExpressHomeController extends Controller
{
    public function home ()
    {
        $menus = Menu::getActiveMenusOrderByPopularity();
        return view('express.index', [
            "menus" => $menus
        ]);
    }
}
