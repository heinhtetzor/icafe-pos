<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuGroup;
use App\Order;
use App\Waiter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpressHomeController extends Controller
{
    public function home ()
    {
        $menus = Menu::getActiveMenusOrderByPopularity();
        $menu_groups=MenuGroup::getMenuGroups();
        $expressOrders = Order::getExpressOrders();
        $waiters = Waiter::all();

        $existing_express = Order::where('created_at', '>=', Carbon::today()->startOfDay())
        ->where('table_id', 'express')
        ->where('status', 0)
        ->first();
        
        if ($existing_express) 
        {
            return view('express.index', [
                "menus" => $menus,
                "waiters" => $waiters,
                "menu_groups" => $menu_groups,
                "order" => $existing_express
            ]);        
        }
        else 
        {
            return view('express.create', [
                "expressOrders" => $expressOrders
            ]);
        }

    }

    public function show ($id)
    {
        $order=Order::findorfail($id);
        $orderMenus=$this->getOrderMenusGrouped($order);
        $total=$orderMenus->sum(function($t) {
            return $t->quantity*$t->price;
        });
        return view('express.show', [
            'order'=>$order,
            'orderMenus'=>$orderMenus,
            'total'=>$total
        ]);
    }

    public function create () //create new session
    {
        $expressOrders = Order::getExpressOrders();    
        return view('express.create', [
            "expressOrders" => $expressOrders
        ]);
    }

    public function store (Request $request)
    {
        Order::create([
            "status" => 0,
            "table_id" => "express",
            "invoice_no" => Order::generateInvoiceNumber()
        ]);
        return redirect('/admin/express');
    }
}
