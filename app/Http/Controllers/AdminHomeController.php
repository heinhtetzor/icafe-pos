<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
class AdminHomeController extends Controller
{
    use AuthenticatesUsers;
        protected $redirectTo = '/admin';

        // admin Login controller
        function showAdminLogin() {
            return view('admin.login');
        }
        function adminLogin(Request $request) {      
            if(Auth::guard('admin_account')->attempt($request->only('username', 'password'))) {
                return redirect()
                ->intended(route('admin.home'))
                ->with('success', 'You are logged in as Admin');
            }
            return redirect()
            ->back()
            ->withInput($request->only('username'))
            ->with('error', 'The credentials do not match our records.');
        }
        
        function adminLogout() {
            Auth::guard('admin_account')->logout();
            return redirect()
            ->route('admin.showLogin')
            ->with('success', 'You are logged out!');
        }
        public function username()
        {            
            return 'username';
        }



    function admin() {
        return view("admin.index");
    }
    function accountmanagement() {
        return view("admin.accountmanagement");
    }
    function masterdatamanagement() {
        return view("admin.masterdatamanagement");
    }
}
