<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Table;
use App\Menu;
use App\Order;
use App\MenuGroup;
use App\Waiter;
use App\Http\Traits\OrderFunctions;
use App\Setting;
use App\TableGroup;

class AdminHomeController extends Controller
{
    use AuthenticatesUsers;
    use OrderFunctions;
        protected $redirectTo = '/admin';

        // admin Login controller
        function showAdminLogin() {
            return view('admin.login');
        }
        function adminLogin(Request $request) {      
            if(Auth::guard('admin_account')->attempt($request->only('username', 'password'))) {
                return redirect()
                ->intended(route('admin.home'))
                ->with('success', 'You are logged in as Admin');
            }
            return redirect()
            ->back()
            ->withInput($request->only('username'))
            ->with('error', 'The credentials do not match our records.');
        }
        
        function adminLogout() {
            Auth::guard('admin_account')->logout();
            return redirect()
            ->route('admin.showLogin')
            ->with('success', 'You are logged out!');
        }
        public function username()
        {            
            return 'username';
        }

        //Admin Table List
        //reusing waiter views
        public function tables()
        {
            $store_id = Auth()->guard('admin_account')->user()->store_id;
            $table_groups = TableGroup::where('store_id', $store_id)->get();
            return view('waiter.index', [
                "table_groups" => $table_groups
            ]);
        }

        //Admin Table POS view
        public function pos($id)
        {
            // $menus=Menu::getActiveMenus();
            $store_id = Auth()->guard('admin_account')->user()->store_id;
            $menus=Menu::getActiveMenus($store_id);
            $menu_groups=MenuGroup::getMenuGroups($store_id);
            $waiters=Waiter::where('store_id', $store_id)->get();
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
                "waiters"=>$waiters,
                "tableId"=>$id,
                "table"=>$table,
                "current_order"=>$currentOrder,
                "order_menus"=>$order_menus,
                "total"=>$total           
            ]);
        }
        function orders($orderId) {
            $store_id = Auth()->guard('admin_account')->user()->store_id;
            $order=Order::findorfail($orderId);
            $passcode=Setting::getPasscode($store_id);
            $orderMenus=$order->order_menus;
            return view("waiter.orders", [
                'orderMenus'=>$orderMenus,
                'passcode'=>$passcode
            ]);
        }
    function admin() {
        return view("admin.index");
    }
    function accountmanagement() {
        return view("admin.accountmanagement");
    }
    function masterdatamanagement() {
        return view("admin.masterdatamanagement");
    }
}
