<?php

namespace App\Http\Controllers;

use App\Customer;
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


        //reusing waiter views
        public function tables()
        {
            $table_groups = TableGroup::all();
            return view('waiter.index', [
                "table_groups" => $table_groups
            ]);
        }

        public function pos($tableId)
        {
            // $menus=Menu::getActiveMenus();
            $menus=Menu::getActiveMenus();
            $menu_groups=MenuGroup::getMenuGroups();
            $waiters=Waiter::all();
            $customers = [];
            
            $table = null;
            if ($tableId != -1) {
                $table=Table::findorfail($tableId);
                $customers = Customer::orderBy('name', 'ASC')->get();
            }
            // dd($id);
            $currentOrder=$this->getActiveOrder($tableId);
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
                "customers"=>$customers,
                "tableId"=>$tableId,
                "table"=>$table,
                "current_order"=>$currentOrder,
                "order_menus"=>$order_menus,
                "total"=>$total           
            ]);
        }
        function orders($orderId) {
            $order=Order::findorfail($orderId);
            $passcode=Setting::getPasscode();
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
