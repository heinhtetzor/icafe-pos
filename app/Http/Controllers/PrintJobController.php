<?php

namespace App\Http\Controllers;

use App\PrintJob;
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

        return response()->json([
            "data" => $data_list
        ]);
    }

    public function updateToPending (Request $request) {
        $print_job = PrintJob::findOrFail($request->print_job_id);
        $print_job->status = PrintJob::STATUS_PENDING;
        $print_job->save();

        return response()->json([
            "success" => true
        ]);
    }
}
