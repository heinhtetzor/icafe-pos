<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class MobileServerController extends Controller
{
    public function start(Request $request)
    {
        $localIP = getHostByName(getHostName());

        exec("cd .. && php artisan serve --host=0.0.0.0");

        return response()->json([
            "message" => "Running"
        ]);

    } 
}
