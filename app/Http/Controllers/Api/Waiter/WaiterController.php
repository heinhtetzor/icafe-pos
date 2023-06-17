<?php

namespace App\Http\Controllers\Api\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Waiter;

class WaiterController extends Controller
{
    public function index() {
        $store_id = Auth()->guard('admin_account')->user()->store_id;
        $waiters = Waiter::where('store_id', $store_id)
        ->where('status', 1)
        ->get();
        return $waiters->toJson();
    }
}
