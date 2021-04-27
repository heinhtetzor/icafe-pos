<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index ()
    {        
        $passcode = Setting::where('key', 'passcode')->first()->value;
        return view('admin.settings.index', [
            "passcode" => $passcode
        ]);
    }

    public function save (Request $request)
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
}
