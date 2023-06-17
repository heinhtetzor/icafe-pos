<?php

namespace App\Http\Controllers;

use App\MenuGroup;
use App\Order;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Traits\OrderFunctions;
use App\Waiter;
use App\OrderMenu;
use App\Setting;

class OrderController extends Controller
{
    use OrderFunctions;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    //entry point to other pages
    public function index()
    {
        return view('admin.orders.index');
    }
    //$id = orderId
    public function day(Request $request) {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        if ($request->has('invoiceNo')) {
            $order = Order::where('store_id', $store_id)
            ->where('invoice_no', $request->invoiceNo)->first();  
            if (is_null($order)) {
                return redirect()->back()->with('msg', 'မရှိပါ');
            }
            return $this->show($request, $order->id);
        }


        $fromTime = null;
        $toTime = null;
        $isToday=FALSE;
        if($request->has('date')) {
            // $from=date($request->date)->startOfDay(); 
            $from=explode(" - ", $request->date)[0];
            $to=explode(" - ", $request->date)[1];
            $fromTime=Carbon::parse($from)->startOfDay();
            $toTime=Carbon::parse($to)->endOfDay();
        }
        else {
            $fromTime=now()->startOfDay();
            $toTime=now()->endOfDay();
            $isToday=TRUE;
        }
        // dd($fromTime, $toTime);
        //get today orders
        $orders=Order::where('store_id', $store_id)
                ->whereBetween('created_at', [$fromTime, $toTime])
                ->orderBy('created_at', 'DESC')
                ->simplePaginate(20);

        return view('admin.orders.day', [
            'orders'=>$orders,
            'fromTime'=>$fromTime,
            'toTime'=>$toTime,
            'isToday'=>$isToday
        ]);
    }

    public function calendar(Request $request) {
        if($request->get('date')) {
            dd($request->get('date'));
        }
        return view('admin.orders.calendar');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {        
        $is_edit_mode = false;
        if ($request->edit == "true") {
            $is_edit_mode = true;
        }
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $passcode = Setting::getPasscode($store_id);
        
        $order=Order::findorfail($id);
        $orderMenus=$this->getOrderMenusGrouped($order);
        $orderMenuGroups = $this->getSummaryByOrder($order->id);

        if ($order->total > 0) {
            $total = $order->total;
        }
        else {
            $total=$orderMenus->sum(function($t) {
                return $t->quantity*$t->price;
            });
        }
        
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $waiters = Waiter::where('store_id', $store_id)->get();

        //group order menus with menu group total lines
        $orderMenusGrouped = [];
        $menu_group_order_menus_map = [];
        $menu_groups = []; //for mapping menu_group_id and name
        foreach ($orderMenus as $orderMenu) {
            if (array_key_exists($orderMenu->menu_group_id, $menu_group_order_menus_map)) {
                array_push($menu_group_order_menus_map[$orderMenu->menu_group_id] ,$orderMenu);
            } else {
                $menu_group_order_menus_map[$orderMenu->menu_group_id] = [$orderMenu];
                $menu_groups[$orderMenu->menu_group_id] = $orderMenu->menu_group_name;
            }
        }
        foreach ($menu_group_order_menus_map as $menu_group_id => $orderMenus) {
            $menu_group_total = 0;
            $menu_group_qty = 0;
            foreach ($orderMenus as $orderMenu) {
                array_push($orderMenusGrouped, $orderMenu);
                $menu_group_total += $orderMenu->quantity * $orderMenu->price;
                $menu_group_qty += $orderMenu->quantity;
            }
            $menu_group_summary = (object)[
                "isSummary" => true,
                "menuGroupName" => $menu_groups[$menu_group_id],
                "menuGroupQty" => $menu_group_qty,
                "menuGroupTotal" => $menu_group_total
            ];
            array_push($orderMenusGrouped, $menu_group_summary);
        }
        
        return view('admin.orders.show', [
            'order'=>$order,
            'orderMenus'=>$orderMenusGrouped,
            'orderMenuGroups'=>$orderMenuGroups,
            'total'=>$total,
            'is_edit_mode'=>$is_edit_mode,
            'passcode' => $passcode,
            'waiters' => $waiters
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        try {
            $section = explode("/", $request->getRequestUri());

            $order = Order::findorfail($id);
            
            OrderMenu::where('order_id', $id)->delete();
            
            $order->delete();
            
            if ($section[2] === "orders") 
            {
                return redirect("/admin/reports/day");
            }
            if ($section[2] === "express") 
            {
                return redirect()->back();
            }
        }
        catch (\Exception $e) {
            return redirect()->back()->with("error", "ဖျက်လို့မရပါ");
        }
    }
}
