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
use Illuminate\Foundation\Auth\AuthenticatesUsers;
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
        $tables=Table::getTablesAsc();
        return view("waiter.index", [
            "tables"=>$tables
        ]);
    }

    //POS view for waiter
    // $id = tableId
    function pos($id) {
        $menus=Menu::getActiveMenus();
        $menu_groups=MenuGroup::getMenuGroups();
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
        return view("waiter.orders", [
            'orderMenus'=>$orderMenus
        ]);
    }
    
}
