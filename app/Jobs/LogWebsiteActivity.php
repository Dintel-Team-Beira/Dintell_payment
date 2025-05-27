<?php
namespace App\Jobs;

use App\Models\ApiLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogWebsiteActivity implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logData;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    public function handle()
    {
        try {
            ApiLog::create($this->logData);
        } catch (\Exception $e) {
            \Log::error('Failed to create API log', [
                'error' => $e->getMessage(),
                'data' => $this->logData
            ]);
        }
    }

    public function failed(\Exception $exception)
    {
        \Log::error('LogWebsiteActivity job failed', [
            'error' => $exception->getMessage(),
            'data' => $this->logData
        ]);
    }
}
