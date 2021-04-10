<?php

namespace App\Http\Controllers;

use App\Kitchen;
use Illuminate\Http\Request;
use Auth;

class KitchenHomeController extends Controller
{
    function showKitchenLogin() {
    	return view('kitchen.login');
    }
    function home() {
    	return view('kitchen.index');
    }
    function adjustPanelSize (Request $request) {               
        Kitchen::findorfail($request->id)->update([
            "font_size" => $request->font_size,
            "panel_size" => $request->panel_size
        ]);
        return redirect(route('kitchen.home'));
    }
    function kitchenLogin(Request $request) {                  
        if(Auth::guard('kitchen')->attempt($request->only('username', 'password'))) {
            return redirect()
            ->intended(route('kitchen.home'))
            ->with('success', 'You are logged in as Kitchen');
        }
        return redirect()
        ->back()
        ->withInput($request->only('username'))
        ->with('error', 'The credentials do not match our records.');
    }
    function kitchenLogout() {
        Auth::guard('kitchen')->logout();
        return redirect()
        ->route('kitchen.showLogin')
        ->with('success', 'You are logged out!');
    }
    public function username()
    {
        return 'username';
    }
}
