<?php

namespace App\Http\Controllers;
use App\Order;
use App\PrintJob;
use App\Services\PrintJobService;
use App\Services\PrintService;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function printOrderSummary (Order $order)
    {                
        try {
            PrintService::printOrderSummary($order);
            return redirect()->back();
        }
        catch (\Exception $e) {
            return redirect()->back()->with("error", "Print ထုတ်၍မရပါ");
        }
    }

    public function printOrderBill (Order $order)
    {
        try {
            PrintJobService::createPendingJob($order->store_id, PrintJob::TYPE_ORDER_BILL, $order->id);
            return redirect()->back();
        }
        catch (\Exception $e) {            
            return redirect()->back()->with("error", "Print ထုတ်၍မရပါ");
        }
    }

    public function printMenuReport (Request $request) 
    {
        try {
            $store_id = Auth()->guard('admin_account')->user()->store_id;
            PrintService::printOrderMenuReport($request->lines, $store_id);
            return ["message" => "printed"];
        }
        catch (\Exception $e) {
            dd($e);
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }

}
