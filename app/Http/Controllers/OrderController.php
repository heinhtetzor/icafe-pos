<?php

namespace App\Http\Controllers;

use App\MenuGroup;
use App\Order;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Http\Traits\OrderFunctions;

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
        if ($request->has('invoiceNo')) {
            $order = Order::where('invoice_no', $request->invoiceNo)->first();  
            if (is_null($order)) {
                return redirect()->back()->with('msg', 'မရှိပါ');
            }
            return $this->show($order->id);
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
        $orders=Order::orderBy('created_at', 'DESC')
                ->whereBetween('created_at', [$fromTime, $toTime])
                ->simplePaginate(20);
        
        //for summary panel
        $orderMenuGroups=DB::table('order_menus')
                      ->join('menus', 'order_menus.menu_id', '=', 'menus.id')
                      ->join('menu_groups', 'menus.menu_group_id', '=', 'menu_groups.id')
                      ->join('orders', 'orders.id', '=', 'order_menus.order_id')                      
                      ->selectRaw('menu_groups.id as id, menu_groups.name as name, SUM(order_menus.quantity) as quantity, SUM(order_menus.quantity*order_menus.price) as total')
                      ->where('orders.status', '=', '1')                      
                      ->whereBetween('orders.created_at', [$fromTime, $toTime])
                      ->groupBy('menu_groups.id')
                      ->get();        
        return view('admin.orders.day', [
            'orders'=>$orders,
            'orderMenuGroups'=>$orderMenuGroups,
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
    public function show($id)
    {        
        $order=Order::findorfail($id);
        $orderMenus=$this->getOrderMenusGrouped($order);
        $total=$orderMenus->sum(function($t) {
            return $t->quantity*$t->price;
        });
        return view('admin.orders.show', [
            'order'=>$order,
            'orderMenus'=>$orderMenus,
            'total'=>$total
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
    public function destroy($id)
    {
        $order = Order::findorfail($id);
        //temporary solution to delete related         
        foreach ($order->order_menus as $om) {
            $om->delete();
        }
        $order->delete();
        return redirect("/admin/reports/day");
    }
}
