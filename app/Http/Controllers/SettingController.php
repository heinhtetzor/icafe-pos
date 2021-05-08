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

    public function downloadBackupFile ()
    {
        $files = Storage::disk('public')->files('backup');        
        return redirect('/storage/'.$files[0]);
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
