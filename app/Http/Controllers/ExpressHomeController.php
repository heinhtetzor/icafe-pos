<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuGroup;
use App\Order;
use App\Table;
use App\Waiter;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpressHomeController extends Controller
{
    public function home ()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id ?? Auth()->guard('waiter')->user()->store_id;
        $menus = Menu::getActiveMenus($store_id);
        $menu_groups=MenuGroup::getActiveMenuGroups($store_id);
        $expressOrders = Order::getExpressOrders($store_id);
        $waiters = Waiter::where('store_id', $store_id)
        ->where('status', 1)
        ->get();

        $existing_express = Order::where('created_at', '>=', Carbon::today()->startOfDay())
        ->where('store_id', $store_id)
        ->where('table_id', Table::EXPRESS)
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
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $expressOrders = Order::getExpressOrders($store_id);    
        return view('express.create', [
            "expressOrders" => $expressOrders
        ]);
    }

    public function store (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        Order::create([
            'store_id' => $store_id,
            "status" => 0,
            "table_id" => Table::EXPRESS,
            "invoice_no" => Order::generateInvoiceNumber()
        ]);
        return redirect('/admin/express');
    }
}
