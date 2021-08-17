<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index ()
    {
        return view('admin.settings.index');
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
        $settings = Setting::all();
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
        $old_passcode = Setting::getPasscode();
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
        Setting::updateOrCreate([
            "key" => "shop_name"
        ], [
        	"value" => $request->shop_name
        ]);

        Setting::updateOrCreate([
            "key" => "shop_line_1"
        ], [
        	"value" => $request->shop_line_1
        ]);

        Setting::updateOrCreate([
            "key" => "shop_line_2"
        ], [
        	"value" => $request->shop_line_2
        ]);

        Setting::updateOrCreate([
            "key" => "printer_connector"
        ], [
            "value" => $request->printer_connector
        ]);

        Setting::updateOrCreate([
            "key" => "bill_footer_text"
        ], [
            "value" => $request->bill_footer_text
        ]);

        return redirect()->back()->with('msg', "Succesfully updated shop info.");
    }

    public function getAll ()
    {
    	return response()->json([
    		"settings" => Setting::all()
    	]);
    }
}
