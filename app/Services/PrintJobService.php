<?php
namespace App\Services;

use App\OrderMenu;
use App\PrintJob;
use Carbon\Carbon;
use Exception;

/*
* this class is to handle remote print request from local batch job
*/
class PrintJobService {

    public static function getPendingJobs ($storeId) {
        return PrintJob::where('store_id', $storeId)
        ->where('status', PrintJob::STATUS_PENDING)
        ->get();
    }

    public static function createPendingJob ($storeId, $type, $id) {
        switch ($type) {
            case PrintJob::TYPE_ORDER_BILL:
                PrintJob::create([
                    "store_id" => $storeId,
                    "type" => $type,
                    "order_id" => $id
                ]);
                break;
                
            case PrintJob::TYPE_ORDER_MENU_TABLE_SLIP:
                $orderMenu = OrderMenu::findOrFail($id);
                if ($orderMenu->menu->menu_group->print_slip != 1) {
                    break;
                }
                PrintJob::create([
                    "store_id" => $storeId,
                    "type" => $type,
                    "order_menu_id" => $id
                ]);
                break;

            case PrintJob::TYPE_ORDER_MENU_EXPRESS_SLIP;
                $orderMenu = OrderMenu::findOrFail($id);
                if ($orderMenu->menu->menu_group->print_slip != 1) {
                    break;
                }
                PrintJob::create([
                    "store_id" => $storeId,
                    "type" => $type,
                    "order_menu_id" => $id
                ]);
                break;

            case PrintJob::TYPE_EXPENSE:
                PrintJob::create([
                    "store_id" => $storeId,
                    "type" => $type,
                    "expense_id" => $id
                ]);
            
            default:
                throw new Exception("Type is required.");
        }
    }

    public static function processJob (PrintJob $printJob) {
        if ($printJob->type == PrintJob::TYPE_ORDER_MENU_EXPRESS_SLIP) {
            $printJob->status = PrintJob::STATUS_SUCCESS;
            $printJob->save();
            return self::processOrderMenuExpressSlip($printJob->orderMenu);
        }
    }
    
    protected static function processOrderMenuExpressSlip (OrderMenu $orderMenu) {
        $order_menu_id = $orderMenu->id;
        $menu = $orderMenu->menu;
        $menu_id = $menu->id;
        $menu_name = $menu->name;
        $store_id = $menu->store_id;
        $qty = $orderMenu->quantity;
        $datetime = Carbon::parse($orderMenu->created_at)->format('h:i A d-M-Y');
        $waiter = $orderMenu->waiter->name ?? "";
        
        return (object)[
            "store_id" => $store_id,
            "order_menu_id" => $order_menu_id,
            "menu_id" => $menu_id,
            "menu_name" => $menu_name,
            "qty" => $qty,
            "datetime" => $datetime,
            "waiter" => $waiter,
            "type" => PrintJob::TYPE_ORDER_MENU_EXPRESS_SLIP
        ];
    }
}