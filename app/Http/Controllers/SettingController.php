<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index (Request $request)
    {
        $localIP = getHostByName(getHostName());   

        return view('admin.settings.index', [
            "ip_address" => $localIP
        ]);
    }

    public function passcode ()
    {        
        $passcode = Setting::where('key', 'passcode')->first()->value;
        return view('admin.settings.passcode', [
            "passcode" => $passcode
        ]);
    }
    public function shop ()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $settings = Setting::where('store_id', $store_id)->get();
        $shop_name = "";
        $shop_line_1 = "";
        $shop_line_2 = "";
        $printer_connector = "";
        $bill_footer_text = "";

        foreach ($settings as $setting)
        {
            if ($setting->key === "shop_name") {
                $shop_name = $setting->value;
            }
            if ($setting->key === "shop_line_1") {
                $shop_line_1 = $setting->value;
            }
            if ($setting->key === "shop_line_2") {
                $shop_line_2 = $setting->value;
            }
            if ($setting->key === "printer_connector") {
                $printer_connector = $setting->value;
            }
            if ($setting->key === "bill_footer_text") {
                $bill_footer_text = $setting->value;
            }

        }

        return view ('admin.settings.shop', [
            "settings" => $settings,
            "shop_name" => $shop_name,
            "shop_line_1" => $shop_line_1,
            "shop_line_2" => $shop_line_2,
            "printer_connector" => $printer_connector,
            "bill_footer_text" => $bill_footer_text
        ]);
    }

    public function downloadBackupFile ()
    {
        $files = Storage::disk('public')->files('backup');        
        return redirect('/storage/'.$files[0]);
    }

    public function savePasscode (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $old_passcode = Setting::getPasscode($store_id);
        if ($request->old_passcode_value !== $old_passcode) {
            return redirect()->back()->with('error', "Wrong old passcode.");
        }
        
        if ($request->passcode_value) 
        {
            Setting::updateOrCreate([
                "key" => "passcode"
            ], [
                "value" => $request->passcode_value
            ]);
        }
        return redirect()->back()->with('msg', "Succesfully updated passcode.");
    }

    public function saveShop (Request $request)
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        Setting::updateOrCreate([
            "key" => "shop_name",
            "store_id" => $store_id
        ], [
        	"value" => $request->shop_name
        ]);

        Setting::updateOrCreate([
            "key" => "shop_line_1",
            "store_id" => $store_id
        ], [
        	"value" => $request->shop_line_1
        ]);

        Setting::updateOrCreate([
            "key" => "shop_line_2",
            "store_id" => $store_id
        ], [
        	"value" => $request->shop_line_2
        ]);

        Setting::updateOrCreate([
            "key" => "printer_connector",
            "store_id" => $store_id
        ], [
            "value" => $request->printer_connector
        ]);

        Setting::updateOrCreate([
            "key" => "bill_footer_text",
            "store_id" => $store_id
        ], [
            "value" => $request->bill_footer_text
        ]);

        return redirect()->back()->with('msg', "Succesfully updated shop info.");
    }

    public function getAll ()
    {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
    	return response()->json([
    		"settings" => Setting::where('store_id', $store_id)->get()
    	]);
    }
}
