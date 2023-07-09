<?php

namespace App\Console\Commands;

use App\PrintJob;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePrintJobStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-print-job-status:process {minute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating pending print jobs older than XX minutes to Done status (1)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $minute = $this->argument('minute');

        \Log::info("[Scheduler - Update Print Job Status] Getting pending jobs to update");
        PrintJob::where('status', 0)
        ->where('created_at', '<', Carbon::now()->subMinutes($minute))
        ->update([
            "status" => PrintJob::STATUS_SUCCESS
        ]);
        \Log::info("[Scheduler - Update Print Job Status] Updating pending jobs finished");
    }
}
