<?php

namespace App\Http\Controllers;

use App\Mail\OrderMailable;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Http\Traits\OrderFunctions;
use Exception;

class EmailController extends Controller
{
    use OrderFunctions;

    public function sendOrder (Request $request) {

        if (empty($request->email)) {
            throw new Exception("Email is required");
        }

        $order_id = $request->order_id;
        $emails = explode(',', $request->email);

        //send email
        $order = Order::findOrFail($order_id);
        $orderMenus = $this->getOrderMenusGrouped($order);

        $total=$orderMenus->sum(function($t) {
            return $t->quantity*$t->price;
        });

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

        Mail::to($emails)->send(new OrderMailable($orderMenusGrouped, $order, $total));

        return response()->json([
            "success" => true
        ]);
    }
}
