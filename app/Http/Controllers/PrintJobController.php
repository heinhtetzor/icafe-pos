<?php

namespace App\Http\Controllers;

use App\Services\PrintJobService;
use Illuminate\Http\Request;

class PrintJobController extends Controller
{
    public function process (Request $request) {
        $pendingJobs = PrintJobService::getPendingJobs($request->store_id);

        $data_list = [];
        foreach ($pendingJobs as $pendingJob) {
            $data = PrintJobService::processJob($pendingJob);
            array_push($data_list, $data);
        }

        return response()->json($data_list);
    }
}
