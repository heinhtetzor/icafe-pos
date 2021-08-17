<?php

namespace App\Http\Controllers;
use App\Order;
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
            PrintService::printOrderBill($order);
            return redirect()->back();
        }
        catch (\Exception $e) {            
            return redirect()->back()->with("error", "Print ထုတ်၍မရပါ");
        }
    }

    public function printMenuReport (Request $request) 
    {
        try {
            // dd("$request->lines");
            PrintService::printOrderMenuReport($request->lines);
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
