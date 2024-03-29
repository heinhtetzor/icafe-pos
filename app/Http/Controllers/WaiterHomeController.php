<?php

namespace App\Http\Controllers;

use App\Menu;
use App\MenuGroup;
use App\Table;
use App\TableStatus;
use App\Order;
use App\Waiter;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Http\Traits\OrderFunctions;
use App\Setting;
use App\TableGroup;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Carbon;
use PhpParser\Node\Expr\Cast\Array_;

class WaiterHomeController extends Controller
{
    use OrderFunctions;
    use AuthenticatesUsers;
    protected $redirectTo = '/waiter';

    function showWaiterLogin() {
        return view('waiter.login');
    }
    
    function waiterLogin(Request $request) {      
            
        if(Auth::guard('waiter')->attempt($request->only('username', 'password'))) {
            return redirect()
            ->intended(route('waiter.home'))
            ->with('success', 'You are logged in as Waiter');
        }
        return redirect()
        ->back()
        ->withInput($request->only('username'))
        ->with('error', 'The credentials do not match our records.');
    }

    function waiterLogout() {
        Auth::guard('waiter')->logout();
        return redirect()
        ->route('waiter.showLogin')
        ->with('success', 'You are logged out!');
    }

    public function username()
    {
        return 'username';
    }

    //home aka Tables list
    function home() {       
        $store_id = Auth()->guard('waiter')->user()->store_id; 
        $existing_express = Order::where('store_id', $store_id)
        ->where('created_at', '>=', Carbon::today()->startOfDay())
        ->where('table_id', Table::EXPRESS)
        ->where('status', 0)
        ->first();
        return view("waiter.index", [            
            "existing_express" => $existing_express
        ]);
    }

    //POS view for waiter
    // $id = tableId
    function pos($id) {
        $store_id = Auth()->guard('waiter')->user()->store_id;
        $menus=Menu::getActiveMenus($store_id);
        $menu_groups=MenuGroup::getActiveMenuGroups($store_id);
        $table=Table::findorfail($id);
        $currentOrder=$this->getActiveOrder($id);
        $order_menus=Array();
        $total=0;
        if($currentOrder) {
            $order_menus=$this->getOrderMenusGrouped($currentOrder);
            $total=$order_menus->sum(function($t) {
                return $t->quantity*$t->price;
            });
            // $total=$order_menus->sum(\DB::raw('(order_menus.price*order_menus.quantity)'));
        }        
        // dd($order_menus);
        return view("waiter.pos", [
            "currentWaiter"=>Waiter::getCurrentWaiter(),
            "menus"=>$menus,
            "menu_groups"=>$menu_groups,
            "tableId"=>$id,
            "table"=>$table,
            "current_order"=>$currentOrder,
            "order_menus"=>$order_menus,
            "total"=>$total           
        ]);
    }

    // waiter viewing orders
    function orders($orderId) {
        $order=Order::findorfail($orderId);
        $orderMenus=$order->order_menus;
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $passcode=Setting::getPasscode($store_id);
        return view("waiter.orders", [
            'orderMenus'=>$orderMenus,
            'passcode'=>$passcode
        ]);
    }
    
}
