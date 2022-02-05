<?php

namespace App\Http\Controllers\Api\Waiter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Waiter;

class WaiterController extends Controller
{
    public function index() {
        $waiters = Waiter::all();
        return $waiters->toJson();
    }
}
